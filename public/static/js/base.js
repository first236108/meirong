/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/6/3 1:45
 */
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-center",
    showDuration: 200,
    extendedTimeOut: 200,
    hideDuration: 600,
    timeOut: 2000,
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "slideDown",
    hideMethod: "slideUp"
};

// 修改指定表的指定字段值 按钮点击切换是否
function changeTableVal(table, id_name, id_value, field, event, text) {
    var obj = event.currentTarget;
    var val = $(obj).data('val');
    var current = $(obj).data('current');
    $.ajax({
        url: "/admin/index/changeTableVal.html",
        data: {
            "table": table,
            "id_name": id_name,
            "id_value": id_value,
            "field": field,
            "value": val
        },
        type: 'POST',
        success: function (data) {
            if (data) {
                // toastr.success('更新成功');
                if ($(obj).hasClass('no')) {
                    $(obj).removeClass('no').addClass('yes').html("<i class='fa fa-check-circle'></i>" + text);
                } else if ($(obj).hasClass('yes')) {
                    $(obj).removeClass('yes').addClass('no').html("<i class='fa fa-ban'></i>" + text);
                } else {
                    //
                }
                $(obj).data('current', val);
                $(obj).data('val', current);
            } else {
                toastr.warning('修改失败...');
            }
        },
        error: function (e) {
            toastr.error('网络错误，请稍后再试 !');
        }
    });
}

// 修改指定表的指定字段值 input
function changeTableVal2(table, id_name, id_value, field, obj) {
    val = $.trim($(obj).val());
    if (!val) return false;
    $.ajax({
        url: "/admin/index/changeTableVal.html",
        data: {
            "table": table,
            "id_name": id_name,
            "id_value": id_value,
            "field": field,
            "value": val
        },
        type: 'POST',
        success: function (data) {
            if (data)
                toastr.success('更新成功');
            else
                toastr.warning('修改失败...');
        },
        error: function (e) {
            toastr.error('网络错误，请稍后再试 !');
        }
    });
}

function getDataForModal(url) {
    $.ajax({
        type: 'GET',
        url: url,
        success: function (data) {
            if (data.succ == 0) {
                vm.list = data.info;
            } else {
                toastr.error(data.msg);
            }
        },
        error: function (e) {
            err();
        }
    });
}

function delItem(url, val, callback = '', index) {
    swal({
        title: "确定要删除吗?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: 'POST',
            url: url,
            data: {id: val},
            success: function (res) {
                swal.close();
                if (res.succ == 0) {
                    toastr.success(res.msg);
                    if (callback) {
                        eval(callback + '(' + index + ')');
                    } else {
                        vm.init_data.list.splice(index, 1);
                    }
                }
                else
                    toastr.warning(res.msg);
            },
            error: function (e) {
                err();
            }
        });
    });
}

function closeModalComm(obj) {
    $(obj).parents('.modal').modal('hide');
}

/**
 * 解析时间戳
 * @param timestamp
 * @param getDate
 * @param getTime
 * @returns {string}
 */
function dateparse(timestamp, getDate, getTime) {
    var date = new Date(parseInt(timestamp) * 1000);
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    var D = date.getDate() + ' ';
    var h = date.getHours() + ':';
    var m = date.getMinutes() + ':';
    var s = date.getSeconds();
    if (getTime)
        return h + m + s;
    if (getDate)
        return Y + M + D;
    return Y + M + D + h + m + s;
}

/**
 * JSON转数组
 * @param obj
 * @returns {Array}
 */
function objToArray(obj){
    var result=[];
    for (var i in obj) {
        result.push(obj[i]);
    }
    return result;
}

/**
 * 生成随机文件名
 * @returns {string}
 */
function makefilename(){
    var myDate = new Date();
    return myDate.getFullYear().toString()+myDate.getMonth().toString()+myDate.getDate().toString()+myDate.getTime().toString().substring(5,10)+Math.random().toString().substring(2,5);
}

function putb64(token,base64_data,callback) {
    pic = base64_data.replace(/^.*?,/, '');
    var url = "http://upload-z1.qiniup.com/putb64/-1/";
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            eval(callback+'("'+JSON.parse(xhr.responseText).key+'")');
        }
    };
    xhr.open("POST", url, false);
    xhr.setRequestHeader("Content-Type", "application/octet-stream");
    xhr.setRequestHeader("Authorization", "UpToken " + token);
    xhr.send(pic);
}
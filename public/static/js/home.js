function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
    if (arr = document.cookie.match(reg))
        return unescape(arr[2]);
    else
        return null;
}

function login(refer) {
    layer.open({
        type: 1
        , content: '<div class="m-cell">\n' +
            '    <div class="cell-item cell-center-first">\n' +
            '        <div class="cell-center">会员登录</div>\n' +
            '    </div>\n' +
            '    <div class="cell-item">\n' +
            '        <div class="cell-left">手机号：</div>\n' +
            '        <div class="cell-right"><input type="text" name="phone" class="cell-input" placeholder="请输入手机号" autocomplete="off" /></div>\n' +
            '    </div>\n' +
            '    <div class="cell-item">\n' +
            '        <div class="cell-left">密&nbsp;&nbsp;&nbsp;&nbsp;码：</div>\n' +
            '        <div class="cell-right"><input type="password" name="password" class="cell-input" /></div>\n' +
            '    </div>\n' +
            '<button type="button" class="btn-block btn-primary" onclick="check_login(\'' + refer + '\')">登录</button>' +
            '<div id="login-div"><a class="pull-right" onclick="location.href=\'/index/user/reg.html\'">注册</a></div>' +
            '</div>'
        , anim: 'up'
        , style: 'position:fixed; top:3rem; left:4%; width: 92%;  padding:10px 0; border-radius:2%;'
    });

}

function check_login(refer) {
    var phone = $('input[name="phone"]').val();
    var pwd = $('input[name="password"]').val();
    if (!phone || !(/^1[3456789]\d{9}$/.test(phone))) {
        layer.open({skin: 'msg', content: '手机号错误', time: 2});
        return;
    }
    if (!pwd || pwd.length < 6) {
        layer.open({skin: 'msg', content: '密码长度错误', time: 2});
        return;
    }
    var index = layer.open({type: 2, content: '登录中…'});
    $.ajax({
        type: 'POST',
        url: '/index/user/login',
        data: {phone: phone, password: pwd},
        success: function (data) {
            localStorage.setItem('token', data.token);
            layer.closeAll();
            layer.open({
                content: '登录成功', skin: 'msg', time: 1.5, end: function () {
                    location.href = refer;
                }
            });
        },
        error: function (xhr, type) {
            layer.close(index);
            layer.open({content: xhr.responseJSON.msg, skin: 'msg', time: 2});
        }
    });
}
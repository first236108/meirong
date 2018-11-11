/*
 *
 *   INSPINIA - Responsive Admin Theme
 *   version 2.7.1
 *
 */

$(document).ready(function () {
    // Fixed Sidebar
    if ($("body").hasClass('fixed-sidebar')) {
        $('.sidebar-collapse').slimScroll({
            height: '100%',
            railOpacity: 0.9
        });
    }

    // Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small');
    } else {
        $('body').removeClass('body-small');
    }

    // MetisMenu
    $('#side-menu').metisMenu();

    // Collapse ibox function
    $('.collapse-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.children('.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    $('.close-link').on('click', function () {
        var content = $(this).closest('div.ibox');
        content.remove();
    });

    // Fullscreen ibox function
    // $('.fullscreen-link').on('click', function () {
    //     var ibox = $(this).closest('div.ibox');
    //     var button = $(this).find('i');
    //     $('body').toggleClass('fullscreen-ibox-mode');
    //     button.toggleClass('fa-expand').toggleClass('fa-compress');
    //     ibox.toggleClass('fullscreen');
    //     setTimeout(function () {
    //         $(window).trigger('resize');
    //     }, 100);
    // });

    // Close menu in canvas mode
    // $('.close-canvas-menu').on('click', function () {
    //     $("body").toggleClass("mini-navbar");
    //     SmoothlyMenu();
    // });

    // Run menu of canvas
    // $('body.canvas-menu .sidebar-collapse').slimScroll({
    //     height: '100%',
    //     railOpacity: 0.9
    // });

    // Open close right sidebar
    $('.right-sidebar-toggle').on('click', function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });

    // Initialize slimscroll for right sidebar
    $('.sidebar-container').slimScroll({
        height: '100%',
        railOpacity: 0.4,
        wheelStep: 10
    });

    // Open close small chat 右下角客服聊天
    // $('.open-small-chat').on('click', function () {
    //     $(this).children().toggleClass('fa-comments').toggleClass('fa-remove');
    //     $('.small-chat-box').toggleClass('active');
    // });

    // Initialize slimscroll for small chat 右下角客服聊天
    // $('.small-chat-box .content').slimScroll({
    //     height: '234px',
    //     railOpacity: 0.4
    // });

    // Append config box / Only for demo purpose
    // Uncomment on server mode to enable XHR calls
    //$.get("skin-config.html", function (data) {
    //    if (!$('body').hasClass('no-skin-config'))
    //        $('body').append(data);
    //});

    // Minimalize menu
    $('.navbar-minimalize').on('click', function (event) {
        event.preventDefault();
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });

    // Tooltips demo
    $('.tooltip-demo').tooltip({
        selector: "[data-tip=tooltip]",
        container: "body"
    });

    fix_height();

    // Move right sidebar top after scroll
    $(window).scroll(function () {
        if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) {
            $('#right-sidebar').addClass('sidebar-top');
        } else {
            $('#right-sidebar').removeClass('sidebar-top');
        }
    });

    $("[data-toggle=popover]")
        .popover();

    // Add slimscroll to element
    $('.full-height-scroll').slimscroll({
        height: '100%'
    });
});


// Minimalize menu when screen is less than 768px
$(window).bind("resize", function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small');
    } else {
        $('body').removeClass('body-small');
    }
});

$(window).bind("scroll", function () {
    if (!$("body").hasClass('body-small')) {
        fix_height();
    }
});
// Full height of sidebar
function fix_height() {
    var heightWithoutNavbar = $("body > #wrapper").height() - 61;
    $(".sidebar-panel").css("min-height", heightWithoutNavbar + "px");

    var navbarheight = $('nav.navbar-default').height();
    var wrapperHeight = $('#page-wrapper').height();

    if (navbarheight > wrapperHeight) {
        $('#page-wrapper').css("min-height", navbarheight + "px");
    }

    if (navbarheight < wrapperHeight) {
        $('#page-wrapper').css("min-height", $(window).height() + "px");
    }

    if ($('body').hasClass('fixed-nav')) {
        if (navbarheight > wrapperHeight) {
            $('#page-wrapper').css("min-height", navbarheight + "px");
        } else {
            $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
        }
    }

}
function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        $('#side-menu').hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 200);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu').hide();
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        $('#side-menu').removeAttr('style');
    }
}

function err(msg) {
    var def = typeof msg !== 'undefined' ? msg : '网络异常，请稍后再试!';
    toastr.error(def);
}



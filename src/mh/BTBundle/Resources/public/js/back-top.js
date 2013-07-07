$(document).ready(function() {
    /* xak for safari */
    if( $.browser.safari ) $(".right_side").css("width", "300px" );
    /* menu fixed */
    $('.head_menu > ul').wrapAll('<div class="fixed"></div>')
    $(window).scroll(function () {
        if ($(window).scrollTop() > 75) {
            $('.fixed').css({position: 'fixed', top: '0px'});
        } else {
            $('.fixed').css({position: 'static', top: '0px'});
        }
    });
    /* end menu fixed */
    /* scroll to top */
    $(".back-top").hide();
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 350) {
                $('.back-top').fadeIn();
            } else {
                $('.back-top').fadeOut();
            }
        });
        $('.back-top').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });/* end scroll to top */
    /* begin tabs */
    $('ul.tabs').delegate('li:not(.current)', 'click', function() {
        $(this).addClass('current').siblings().removeClass('current')
        .parents('div.section').find('div.box').eq($(this).index()).fadeIn(150).siblings('div.box').hide();
    })  /* end tabs */

    /* drop box */
    $('.expand_cont li > #expand').click(function(){
        $(this).toggleClass('expand_down');
        $(this).parent('li').children('div').toggle({'display':'block'}, 1500);
    });
    /* end drop box */
});
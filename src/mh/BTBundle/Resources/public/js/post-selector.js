/*выравнивание табов*/
function setEqualHeight(columns) {
    var tallestcolumn = 0;
    columns.each(
        function() {
            currentHeight = $(this).height();
            if(currentHeight > tallestcolumn) {
                tallestcolumn = currentHeight;
            }
        }
    );
    columns.height(tallestcolumn);
}
$(document).ready(function() {
    setEqualHeight($(".content-wiev_more .tabs > li"));
    liheight = $(".content-wiev_more .tabs > li").height();
    $(".content-wiev_more .tabs > li").css('line-height', liheight+'px');

    setEqualHeight($(".right_side .tabs > li"));
    liheight = $(".right_side .tabs > li").height();
    $(".right_side .tabs > li").css('line-height', liheight+'px');
    $(".post_tab").css('margin-bottom', liheight+37+'px');

});
// @codekit-prepend "vendor/jquery.js";
// @codekit-prepend "vendor/bootstrap.js";
// @codekit-prepend "vendor/bootstrap-colorpicker.js";

$(function() {
    console.log('hi');
    // assign colors to data attributes and color swatch
    $('.input-color').each(
        function () {
            var $input = $(this).find('input');
            var color = $input.val();
            if (!color) {
                color = '#FFF';
            }
            console.log('color: '+$input.val());
            $(this).find('.add-on i').css('background-color', color);
            $(this).data('color', color);
        }
    );
    $('.input-color').colorpicker();
});
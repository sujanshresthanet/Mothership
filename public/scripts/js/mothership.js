// @codekit-prepend "vendor/jquery.js", "vendor/bootstrap.js", "vendor/bootstrap-colorpicker.js", "vendor/bootstrap-datetimepicker.min.js";

$(function() {
    console.log('hi 0.2');
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

    $('.datetime').datetimepicker();
});
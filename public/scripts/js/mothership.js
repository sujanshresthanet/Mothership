// @codekit-prepend "vendor/jquery.js", "vendor/bootstrap.js", "vendor/image-picker.js", "vendor/bootstrap-colorpicker.js", "vendor/bootstrap-datetimepicker.js";

/**
 * Attaches the color picer to input elements
 *
 * @url http://www.eyecon.ro/bootstrap-colorpicker
 * 
 * @return {[type]}
 */
function moColorPicker() {
    var selector = '.input-color';
    if ($(selector).length) {
        // assign colors to data attributes and color swatch
        // "vendor/bootstrap-colorpicker.js"
        $(selector).each(
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
        $(selector).colorpicker();
    }
}

/**
 * Attaches the image picker to select elements
 * 
 * @url https://github.com/rvera/image-picker
 * 
 * @return void
 */
function moImagePicker() {
    var selector = '.image-picker-group select';
    if ($(selector).length) {
        $(selector).imagepicker();
    }
}

/**
 * Attaches the datetime picker to input elements
 *
 * @url http://tarruda.github.io/bootstrap-datetimepicker/
 * 
 * @return {[type]}
 */
function moDateTimePicker() {
    var selector = '.datetime';
    if ($(selector).length) {
        $(selector).datetimepicker();
    }
}

$(function() {
    moColorPicker();
    moImagePicker();
    moDateTimePicker();
});
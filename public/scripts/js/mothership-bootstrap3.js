// @codekit-prepend "vendor/jquery.js", "vendor/bootstrap3/bootstrap.js", "vendor/image-picker.js", "vendor/bootstrap-colorpicker.js", "vendor/bootstrap-datetimepicker.js", "vendor/redactor.min.js";

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
    var selector = 'form .datetime';
    if ($(selector).length) {
        $(selector).datetimepicker();
    }
    selector = 'form .date';
    if ($(selector).length) {
        $(selector).datetimepicker(
            {
                pickTime: false
            }
        );
    }
    selector = 'form .time';
    if ($(selector).length) {
        $(selector).datetimepicker(
            {
                pickDate: false
            }
        );
    }
}

/**
 * Adds html WYSIWYG editor to textbox
 * 
 * @return void
 */
function moHtml() {
    $('form .html').redactor({
        iframe:     true,
        minHeight:  500,
        css:        '/redactor/style.css'
    });
}


function moFileUploadField () {
    $('.file-input-group')
        .find('.remove-file')
            .not('disabled')
            .click(
                function (event) {
                    event.preventDefault();

                    $btn = $(this);
                    $btn.attr('disabled', true);

                    $parent = $btn.parents('.file-input-group');

                    $text = $parent.find('input[type=text]');
                    $text.attr('disabled', true);
                    $text.addClass('hidden')

                    $file = $parent.find('input[type=file]');
                    $file.attr('disabled', false);
                    $file.removeClass('hidden');
                }
            );
}

$(function() {
    moColorPicker();
    moImagePicker();
    moDateTimePicker();
    moHtml();
    moFileUploadField();
    //$('[data-toggle="tooltip"]').tooltip();
});
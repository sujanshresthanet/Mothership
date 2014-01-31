// @codekit-prepend "vendor/jquery.js", "vendor/bootstrap.min.js", "vendor/image-picker.js", "vendor/bootstrap-colorpicker.js", "vendor/bootstrap-datetimepicker.js", "vendor/redactor.js";

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
        css:        '/redactor/style.css',
        convertDivs: false,
        fileUpload: '/admin/upload/file',
        fileUploadErrorCallback: function(json)
        {
            window.alert(json.error);
        },
        imageUpload: '/admin/upload/image',
        imageUploadErrorCallback: function(json)
        {
            window.alert(json.error);
        }
    });
}

/**
 * Toggle the url/page field
 * 
 * @return void
 */
function moNavItemForm() {
    if ( ! $('.create-item, .edit-item').length)
        return;

    $('[name=url], [name=page_id]')
        .parents('.form-group')
            .hide();

    if ($('[name=type]:checked').val() == 'page') {
        $('[name=page_id]')
            .parents('.form-group')
                .show();
    }
    if ($('[name=type]:checked').val() == 'url') {
        $('[name=url]')
            .parents('.form-group')
                .show();
    }

    $('[name=type]').on(
        'change',
        function () {
            if ($(this).val() == 'page') {
                $('[name=page_id]').parents('.form-group').show();
                $('[name=url]').parents('.form-group').hide();
            }
            if ($(this).val() == 'url') {
                $('[name=url]').parents('.form-group').show();
                $('[name=page_id]').parents('.form-group').hide();
            }
        }
    )
}

$(function() {
    moColorPicker();
    moImagePicker();
    moDateTimePicker();
    moHtml();
    moNavItemForm();
    //$('[data-toggle="tooltip"]').tooltip();
});
/**
 * Плагин для загрузки галлереи через ajax
 */
(function ($) {
    $.fn.imageUploader = function (options) {
        return this.each(function () {
            var file;
            var imageHolder = $($(this).data('preview'));
            var imageLinkHolder = $($(this).data('link-input'));
            var $inputImage = $($(this).data('input'));

            if (window.FileReader) {
                $inputImage.change(function () {
                    var files = this.files;
                    if (!files.length) {
                        return;
                    }
                    file = files[0];
                    if (/^image\/\w+$/.test(file.type)) {
                        // fileReader.readAsDataURL(file);
                        // fileReader.onload = function () {
                        $inputImage.val("");
                        // };

                        var form_data = new FormData();
                        var last_image = imageHolder.attr('src');
                        imageHolder.attr('src', '/images/preloader.gif');
                        form_data.append('image_files', file);
                        $.ajax({
                            type: 'POST',
                            url: $(this).data('upload-url'),
                            data: form_data,
                            processData: false,
                            contentType: false,
                            dataType: "json",
                            success: function (dataResponse) {
                                imageLinkHolder.val(dataResponse.filename);
                                imageHolder.attr('src', dataResponse.preview);
                            },
                            error: function (data) {
                                alert(data.responseText);
                                imageHolder.attr('src', last_image);
                            }
                        });
                    } else {
                        alert("Выберите файл изображения.");
                    }
                });
            }

        });

    };
})(jQuery);
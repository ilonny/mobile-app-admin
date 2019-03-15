$(document).ready(function(){
    var book_id = parseInt($('.audiofiles-container').text());
    renderAudioList(book_id);
    $('.audiofiles-container').html('Загрузка...')
   
    $("#open-audio-modal-btn").on('click', function(){
        renderAudioModal();
    })

    $(document).on('click', '#save-audio-btn', function(){
        if (validateAudioFileForm()) {
            var name = $("#audiobook-name-modal").val();
            var description = $("#audiobook-description-modal").val();
            var file_data = $('#audiobook-file').prop('files')[0];
            var language = $("#audiobook-language-modal").val();
            var form_data = new FormData(); 
            form_data.append('file', file_data);
            form_data.append('name', name);
            form_data.append('description', description);
            form_data.append('book_id', book_id);
            form_data.append('language', language);
            console.log('form_data', form_data);
            $('.modal-footer').html('Загрузка...');
            $.ajax({
                url: '/audio/upload-audio',
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                dataType: 'json',
                success: function(data){
                    $('.modal-footer').html(data.message);
                    renderAudioList(book_id);
                    setTimeout(function(){
                        $('#audio-modal').modal('hide') 
                    }, 2000);
                    console.log('upload audio response: ', data)
                }
            })
        }
    });

    $(document).on('click', '.save-sort', function(){
        var sort_val = $(this).prev().val();
        var audio_id = $(this).data('id');
        $.ajax({
            url: '/audio/save-sort',
            type: "POST",
            data: {
                sort_val: sort_val,
                audio_id: audio_id,
            },
            success: function(){
                renderAudioList(book_id);
            }
        });
    })
    $(document).on('click', '#delete-audio-btn', function(){
        var agree = confirm("Подтвердите действие удаления");
        if (agree){
            var audio_id = $(this).data('id');
            $.ajax({
                url: '/audio/delete-audio',
                type: "GET",
                data: {
                    audio_id: audio_id,
                },
                success: function(){
                    renderAudioList(book_id);
                }
            });
        }
    })

    function validateAudioFileForm(){
        var err = true;
        if ($("#audiobook-name-modal").val() == '') {
            err = false;
            $("#audiobook-name-modal").parent().find('.help-block').html('Заполните это поле');
        } else {
            $("#audiobook-name-modal").parent().find('.help-block').html('');
        }

        if ($("#audiobook-file").val() == '') {
            err = false;
            $("#audiobook-file").parent().find('.help-block').html('Заполните это поле');
        } else {
            $("#audiobook-file").parent().find('.help-block').html('');
        }
        return err;
    }

    function renderAudioList(book_id){
        $.ajax({
            url: '/audio/render-audio-list',
            type: "GET",
            data: {
                book_id: book_id
            },
            success: function(data){
                $('.audiofiles-container').html(data);
            }
        });
    }
    function renderAudioModal(){
        $.ajax({
            url: '/audio/render-audio-modal',
            type: "GET",
            // data: {
            //     book_id: book_id
            // },
            success: function(data){
                $('#audio-modal').html(data);
                $('input[name="book_id"]').val(book_id)
            }
        });
    }
});
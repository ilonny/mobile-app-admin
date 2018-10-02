$(document).ready(function(){
    setTimeout(() => {
        
        $('select[name="audio_book_id"]').change();
    }, 300);
    $('select[name="audio_book_id"]').on('change', function(){
        var val = $(this).val();
        var toc = $(this).data('toc-id');
        var render_place = $(this).parent().next();
        console.log(val);
        $.ajax({
            type: "GET",
            url: "/audio/get-audiofiles",
            data: {
                audioBookId: val,
                toc: toc
            },
            success: function(data){
                $(render_place).html(data);
                $('select[name="audiofile_id"]').trigger('change');
            }
        })
    });

    $(document).on('change', 'select[name="audiofile_id"]', function(){
        var val = $(this).val();
        console.log('select val', val)
        $(this).next().data('audiofile-id', val);
        $(this).next().attr('data-audiofile-id', val);
    });

    $(document).on('click', '.save-toc-relation', function(e){
    // $('.save-toc-relation').on('click', function(e){
        e.preventDefault();
        var audiofile_id = $(this).data('audiofile-id'),
            toc_id = $(this).data('toc-id'),
            audio_book_id = $(this).data('audio-book-id');
        var btn = $(this)
        console.log('audiofile_id', audiofile_id);
        console.log('toc_id', toc_id);
        console.log('audio_book_id', audio_book_id);
        btn.button('loading');
        $.ajax({
            type: "GET",
            url: "/api/set-toc-relations",
            data: {
                audiofile_id: audiofile_id,
                toc_id: toc_id,
                audio_book_id: audio_book_id,
            },
            success: function(data){
                btn.button('reset');
            },
            error: function(data){
                alert("Возникла внутренняя ошибка сервера", data);
                btn.button('reset'); 
            }
        })
    });

});
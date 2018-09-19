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
        }
    });
}

$(document).ready(function(){
    renderAudioList(parseInt($('.audiofiles-container').text()))
    $('.audiofiles-container').html('Загрузка...')
});

$("#open-audio-modal-btn").on('click', function(){
    renderAudioModal();
})
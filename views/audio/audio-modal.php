<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title">Загрузить аудиокнигу</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="control-label" for="audiobook-name-modal">Название</label>
                <input type="text" id="audiobook-name-modal" class="form-control" name="name">
                <div class="help-block"></div>
            </div>
            <div class="form-group">
                <label class="control-label" for="audiobook-description-modal">Короткое описание (не обязательно)</label>
                <input type="text" id="audiobook-description-modal" class="form-control" name="description">
                <div class="help-block"></div>
            </div>
            <div class="form-group">
                <label class="control-label" for="audiobook-file">Аудиофайл</label>
                <input type="file" id="audiobook-file" class="form-control" name="audio_file" accept=".mp3,audio/*">
                <div class="help-block"></div>
            </div>
            <input type="hidden" name="book_id" value="">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            <button type="button" class="btn btn-primary" id="save-audio-btn">Сохранить</button>
        </div>
    </div>
</div>
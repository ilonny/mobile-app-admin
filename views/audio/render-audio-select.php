<select name="audiofile_id" id="audiofile_id" class="form-control" style="display: inline-block; width: auto;">
    <option value="0">Не указано</option>
    <?php foreach ($audiofiles as $model): ?>
        <option <?= ($toc->audiofile_id == $model->id) ? "selected" : ""; ?> value="<?= $model->id; ?>"><?= $model->name; ?></option>
    <?php endforeach; ?>
</select>

<button
        data-audiofile-id="0"
        data-toc-id="<?= $toc->id; ?>"
        data-audio-book-id="<?= $audioBook->id ?>"
        class="btn btn-primary save-toc-relation"
    >
    <span>Сохранить</span>
</button>
<?php if ($audiofiles): ?>
    <table class="table table-striped table-bordered" id="data-table" style="margin: 30px 0;">
        <thead>
            <tr>
                <th>Название</th>
                <th>Описание</th>
                <th>Порядок</th>
                <th>Удалить</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($audiofiles as $key => $model): ?>
                <tr>
                    <td><?= $model->name; ?></td>
                    <td><?= $model->description; ?></td>
                    <td>
                        <input type="text" value="<?= $model->sort; ?>" class="form-control" style="max-width: 100px; display: inline-block;"> 
                        <button class="btn btn-primary save-sort" data-id="<?= $model->id; ?>">Сохранить</button>
                    </td>
                    <td>
                        <button class="btn btn-danger" id="delete-audio-btn" data-id="<?= $model->id; ?>">Удалить</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <h5>Ничего не найдено</h5>
<?php endif; ?>
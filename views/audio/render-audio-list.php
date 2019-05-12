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
    <h5>Не найдено Русских Аудиофайлов</h5>
<?php endif; ?>

<?php if ($audiofiles_eng): ?>
<h4>Аудиофайлы (Английская версия)</h4>
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
            <?php foreach ($audiofiles_eng as $key => $model): ?>
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
    <h5>Не найдено Английских Аудиофайлов</h5>
<?php endif; ?>

<?php if ($audiofiles_es): ?>
<h4>Аудиофайлы (Испанская версия)</h4>
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
            <?php foreach ($audiofiles_es as $key => $model): ?>
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
    <h5>Не найдено Испанских Аудиофайлов</h5>
<?php endif; ?>
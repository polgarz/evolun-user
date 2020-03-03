<?php
use yii\helpers\Url;
?>
<?php if ($responsibleUsers): ?>
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Yii::t('user/widget', 'Responsibles') ?></h3>
        </div>
        <div class="box-body">
            <ul class="list-group list-group-unbordered">
                <?php foreach ($responsibleUsers as $responsible): ?>
                    <li class="list-group-item">
                        <a href="<?= Url::to(['/kid/default/view', 'id' => $responsible->kid_id]) ?>"><?= $responsible->kid->name ?> (<?= $responsible->kid->family ?>)</a> - <?= $responsible->responsible->name ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
<?php endif ?>
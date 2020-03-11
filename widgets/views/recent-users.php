<?php
use yii\helpers\Url;

Yii::$app->getModule($userModuleId);
?>
<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('user/widget', 'Recent volunteers') ?></h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body no-padding">
        <ul class="users-list clearfix">
            <?php foreach ($users as $user): ?>
                <li>
                    <img src="<?= $user->getThumbUploadUrl('image', 's') ?>" class="img-circle" alt="Profilkép">
                    <a class="users-list-name" href="<?= Url::to(['/user/default/view', 'id' => $user->id]) ?>"><?= $user->nickname ?></a>
                    <span class="users-list-date"><?= Yii::$app->formatter->asRelativeTime($user->member_since) ?></span>
                </li>
            <?php endforeach ?>
        </ul>
        <!-- /.users-list -->
    </div>
    <!-- /.box-body -->
</div>
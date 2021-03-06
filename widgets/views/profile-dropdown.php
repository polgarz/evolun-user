<?php
use yii\helpers\Url;

Yii::$app->getModule($userModuleId);
?>
<!-- User Account Menu -->
<li class="dropdown user user-menu">
    <!-- Menu Toggle Button -->
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <!-- The user image in the navbar-->
        <img src="<?= $user->getThumbUploadUrl('image', 'xs') ?>" class="user-image" alt="Profilkép">
        <!-- hidden-xs hides the username on small devices so only the image appears. -->
        <span class="hidden-xs"><?= $user->name ?></span>
    </a>
    <ul class="dropdown-menu">
        <!-- The user image in the menu -->
        <li class="user-header">
            <img src="<?= $user->getThumbUploadUrl('image', 's') ?>" class="img-circle" alt="Profilkép">

            <p>
                <?= $user->name ?>
                <?php if ($user->relativeMemberSince): ?>
                    <small><?= Yii::t('user/widget', 'You are a volunteer since {date}', ['date' => $user->relativeMemberSince]) ?></small>
                <?php endif ?>
            </p>
        </li>

        <!-- Menu Body -->
        <!--
        <li class="user-body">
        </li>
        -->

        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <a href="<?= Url::to(['/user/default/view', 'id' => $user->id]) ?>" class="btn btn-default btn-flat"><?= Yii::t('user/widget', 'Profile') ?></a>
            </div>
            <div class="pull-right">
                <a href="<?= Url::to(['/user/default/logout']) ?>" class="btn btn-default btn-flat"><?= Yii::t('user/widget', 'Log out') ?></a>
            </div>
        </li>
    </ul>
</li>
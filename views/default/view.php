<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model evolun\user\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Volunteers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageHeader'] = ['title' => '&nbsp;'];
?>
<div class="row">
    <div class="col-lg-3 col-md-4">
         <!-- Profile Image -->
        <div class="box box-default">
            <div class="box-body box-profile">
                <a href="<?= $model->image ? $model->getUploadUrl('image') : '#' ?>" <?= $model->image ? 'target="_blank"' : '' ?>><img class="profile-user-img img-responsive img-circle" src="<?= $model->getThumbUploadUrl('image', 's') ?>" alt="<?= Yii::t('user', 'Profile image') ?>"></a>
                <h3 class="profile-username text-center"><?= $model->name ?></h3>

                <p class="text-muted text-center">
                    <?= $model->nickname ?>
                    <?php if (!empty($model->facebook)): ?>
                        <?= Html::a('<i class="fa fa-facebook-square"></i>', $model->facebook, ['target' => '_blank']) ?>
                    <?php endif ?>
                </p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b><?= $model->getAttributeLabel('email') ?></b> <a target="_blank" href="mailto:<?= $model->email ?>" class="pull-right"><?= StringHelper::truncate($model->email, 25) ?></a>
                    </li>
                    <?php if (!empty($model->phone)): ?>
                        <li class="list-group-item">
                            <b><?= $model->getAttributeLabel('phone') ?></b> <a target="_blank" href="tel:<?= $model->phone ?>" class="pull-right"><?= $model->phone ?></a>
                        </li>
                    <?php endif ?>
                    <?php if (!empty($model->skype)): ?>
                        <li class="list-group-item">
                            <b><?= $model->getAttributeLabel('skype') ?></b> <a target="_blank" href="skype:<?= $model->skype ?>?call" class="pull-right"><?= $model->skype ?></a>
                        </li>
                    <?php endif ?>
                    <?php if (!empty($model->birth_date)): ?>
                        <li class="list-group-item">
                            <b><?= $model->getAttributeLabel('birth_date') ?></b> <span class="pull-right"><?= Yii::$app->formatter->asDate($model->birth_date) ?>
                            <?php if (date('m') == date('m', strtotime($model->birth_date))): ?>
                                <i class="fa fa-birthday-cake"></i>
                            <?php endif ?>
                        </span>
                        </li>
                    <?php endif ?>
                    <?php if (!empty($model->member_since)): ?>
                        <li class="list-group-item">
                            <b><?= $model->getAttributeLabel('member_since') ?></b> <span class="pull-right"><?= Yii::$app->formatter->asDate($model->member_since) ?></span>
                        </li>
                    <?php endif ?>
                    <?php if ($model->driving_license !== null): ?>
                        <li class="list-group-item">
                            <b><?= $model->getAttributeLabel('driving_license') ?></b> <span class="pull-right"><?= $model->driving_license ? Yii::t('user', 'Yes') : Yii::t('user', 'No') ?></span>
                        </li>
                    <?php endif ?>
                </ul>
                <?php if (Yii::$app->user->can('manageUsers')): ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('user', 'Update'), ['/user/default/update', 'id' => $model->id], ['class' => 'btn btn-primary btn-block']) ?>
                        </div>
                        <div class="col-xs-6">
                            <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('user', 'Delete'), ['/user/default/delete', 'id' => $model->id], ['class' => 'btn btn-danger btn-block', 'data-confirm' => Yii::t('user', 'Are you sure? Every data belongs this user will be deleted!')]) ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <?php if (!empty(Yii::$app->controller->module->widgets)): ?>
            <?php foreach (Yii::$app->controller->module->widgets as $widget): ?>
                <?= $widget::widget(['user' => $model]) ?>
            <?php endforeach ?>
        <?php endif ?>

    </div>
    <!-- /.col -->
    <div class="col-lg-9 col-md-8">
        <div class="nav-tabs-custom">
            <?php if ($modules): ?>
                <?php foreach ($modules as $id => $module): ?>
                    <?php $items[] = ['label' => $module['title'], 'content' => $module['content']] ?>
                <?php endforeach ?>

                <div class="nav-tabs-custom">
                    <?= Tabs::widget([
                        'items' => $items,
                    ]) ?>
                </div>
            <?php endif ?>
        <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

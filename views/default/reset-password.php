<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
?>

<div class="login-box">
    <div class="login-logo">
        <a href="/"><?= Yii::$app->name ?></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><?= Yii::t('user', 'Set a new password below') ?></p>

        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'passwordRepeat')->passwordInput() ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                <?= Html::a(Yii::t('user', 'Cancel'), [Yii::$app->homeUrl], ['class' => 'btn btn-default']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
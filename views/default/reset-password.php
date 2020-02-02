<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
?>

<div class="login-box">
    <div class="login-logo">
        <a href="/"><b>Önkéntes</b> felület</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Add meg az új jelszavadat</p>

        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'passwordRepeat')->passwordInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Új jelszó mentése', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                <?= Html::a('Mégse', [Yii::$app->homeUrl], ['class' => 'btn btn-default']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
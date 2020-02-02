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
        <p class="login-box-msg">Add meg az email címedet, amivel korábban regisztráltál</p>

        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'email')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Jelszó helyreállítása', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                <?= Html::a('Mégse', [Yii::$app->homeUrl], ['class' => 'btn btn-default']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
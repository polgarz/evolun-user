<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;

$this->registerJs('
    $(".btn-generate").on("click", function() {
        var password = Math.random().toString(36).slice(2);
        $("#user-password").attr("type", "text").val(password).focus().select();
        $("#user-passwordrepeat").attr("type", "text").val(password);
    });
    ');

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin(); ?>
    <div class="box box-default">

        <div class="box-header">
            <h3 class="box-title">Alapadatok</h3>
        </div>

        <div class="box-body">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'birth_date')->widget(DatePicker::classname(), [
                'pluginOptions' => [
                    'autoclose'      => true,
                    'format'         => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>

            <?= $form->field($model, 'member_since')->widget(DatePicker::classname(), [
                'pluginOptions' => [
                    'autoclose'      => true,
                    'format'         => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>

            <?= $form->field($model, 'driving_license')->checkbox() ?>

            <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>
        </div>
    </div>

    <div class="box box-default">

        <div class="box-header">
            <h3 class="box-title">Bejelentkezési adatok</h3>
        </div>

        <div class="box-body">

            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete' => 'new-password']) ?>

            <?= $form->field($model, 'password', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-generate" type="button">Generálás</button>
                        </span>
                    </div>{hint}'
                ])->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password']) ?>

            <?= $form->field($model, 'passwordRepeat')->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password']) ?>

            <?= $form->field($model, 'role')->dropdownList(ArrayHelper::map(Yii::$app->authmanager->getChildRoles(Yii::$app->user->identity->role), 'name', 'description'), ['prompt' => 'Válassz jogosultságot']) ?>

        </div>
    </div>

    <div class="box box-default">

        <div class="box-header">
            <h3 class="box-title">Elérhetőségek</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'phone')->textInput()->hint('Formátum: +36301234567') ?>

            <?= $form->field($model, 'skype')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'facebook')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
        </div>
    </div>


    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'sendEmailAfterCreate')->checkbox() ?>
    <?php endif ?>

    <div class="form-group">
        <?= Html::submitButton('Mentés', ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end(); ?>

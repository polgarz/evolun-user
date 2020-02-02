<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
?>
<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-10">{input}<div class="text-muted" style="margin-top: 3px;">{hint}</div>{error}</div>',
        'labelOptions' => ['class' => 'control-label col-sm-2']
    ],
]); ?>

<?= $form->field($model, 'name')->textInput() ?>

<?= $form->field($model, 'nickname')->textInput()->hint('Hogyan szólítsunk?') ?>

<?= $form->field($model, 'email')->textInput() ?>

<?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password'])->hint('Ha nem akarod módosítani, hagyd üresen') ?>

<?= $form->field($model, 'passwordRepeat')->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password']) ?>

<?= $form->field($model, 'phone')->textInput()->hint('Ajánlott formátum: +36301234567') ?>

<?= $form->field($model, 'birth_date')->widget(DatePicker::classname(), [
    'pluginOptions' => [
        'autoclose'      => true,
        'format'         => 'yyyy-mm-dd',
        'todayHighlight' => true
    ]
]) ?>

<?= $form->field($model, 'skype')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'facebook')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'driving_license')->dropdownList([0 => 'Nincs', 1 => 'Van']) ?>

<?= $form->field($model, 'image', [
    'template' => '
        {label}
        <div class="col-sm-10">
            ' . ($model->image ? '
            <div style="width: 100px; height: 100px; margin-bottom: 5px; background-image: url(' . $model->getThumbUploadUrl('image', 's') . ')">
                <a data-method="post" href="' . Url::to(['delete-own-profile-image', 'id' => $model->id]) . '" class="btn close" style="padding: 0 3px" title="Profilkép törlése"><span aria-hidden="true">×</span></a>
            </div>' : '') . '
            {input}
            {error}
        </div>',
])->fileInput(['accept' => 'image/*']) ?>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <?= Html::submitButton('Mentés', ['class' => 'btn btn-success']) ?>
        <div class="pull-right">
            <?= Html::a('Profilod törlése', ['delete-own-profile', 'id' => $model->id], ['class' => 'btn btn-default', 'data-method' => 'post', 'data-confirm' => 'Biztosan törölni akarod magad a felületről? Minden adatod visszavonhatatlanul törlődni fog!']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
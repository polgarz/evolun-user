<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Workgroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box box-default">

    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput() ?>

        <?= $form->field($model, 'parent')->dropdownList(ArrayHelper::map(Yii::$app->authmanager->getChildRoles(Yii::$app->user->identity->role), 'name', 'name'), ['prompt' => '-']) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'permissions')->checkboxList(ArrayHelper::map($permissions, 'name', function($item) { return $item->description . ' (' . $item->name . ')'; }), ['separator' => '<br />']) ?>

        <div class="form-group">
            <?= Html::submitButton('Mentés', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

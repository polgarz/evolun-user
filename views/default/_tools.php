<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<?php if (Yii::$app->user->can('manageUsers')): ?>
    <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('user', 'New volunteer'), ['create'], ['class' => 'btn btn-success pull-left', 'style' => 'margin-right: 10px']) ?>
<?php endif ?>

<?php $form = ActiveForm::begin(['method' => 'get']); ?>
    <div class="input-group">
        <?= $form->field($searchModel, 'searchString', ['options' => ['tag' => false], 'inputOptions' => ['placeholder' => Yii::t('user', 'Search by email, name, and others')]])->label(false) ?>
        <div class="input-group-btn">
            <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>


<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\user\models\User */

$this->title = Yii::t('user', 'Update volunteer: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Volunteers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Update');
$this->params['pageHeader'] = ['title' => $this->title];
?>

<?= $this->render(Yii::$app->controller->module->userTemplates['form'], [
    'model' => $model,
]);

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\user\models\User */

$this->title = 'Önkéntes adatainak módosítása: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Önkéntesek', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Módosítás';
$this->params['pageHeader'] = ['title' => $this->title];
?>

<?= $this->render(Yii::$app->controller->module->userTemplates['form'], [
    'model' => $model,
]) ?>
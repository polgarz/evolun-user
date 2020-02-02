<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\user\models\User */

$this->title = 'Új önkéntes';
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = ['label' => 'Önkéntesek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render(Yii::$app->controller->module->userTemplates['form'], [
    'model' => $model,
]) ?>


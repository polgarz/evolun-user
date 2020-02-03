<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\user\models\User */

$this->title = Yii::t('user', 'New volunteer');
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Volunteers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render(Yii::$app->controller->module->userTemplates['form'], [
    'model' => $model,
]) ?>


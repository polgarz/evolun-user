<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Workgroup */

$this->title = Yii::t('user/rbac', 'Update role: {role}', ['role' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('user/rbac', 'Roles'), 'url' => ['index']];
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model'        => $model,
    'permissions'  => $permissions,
    'insert'       => false,
]);

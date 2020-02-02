<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Workgroup */

$this->title = 'Jogosultsági kör módosítása: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Jogosultsági körök', 'url' => ['index']];
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model'        => $model,
    'permissions'  => $permissions,
    'insert'       => false,
]) ?>

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'Volunteers');
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-default">

    <div class="box-header">
        <div class="box-tools">
            <?= $this->render(Yii::$app->controller->module->userTemplates['tools'], ['searchModel' => $searchModel]) ?>
        </div>
    </div>

    <div class="box-body table-responsive">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'showHeader' => false,
            'tableOptions' => ['class' => 'table table-hover'],
            'layout' => '{items}{summary}{pager}',
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function($model) use (&$userRoles) {
                        $layout = '
                            <a href="{url}" class="text-default">
                                <div class="media">
                                    <div class="media-left media-middle">
                                        <img src="{image}" class="img-circle" width="40" />
                                    </div>
                                    <div class="media-body">
                                        {name}
                                        <div class="text-muted">
                                            {summary}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        ';

                        return strtr($layout, [
                            '{image}' => $model->getThumbUploadUrl('image', 's'),
                            '{name}' => $model->inactive ? Html::tag('strong', '<i class="fa fa-clock-o" title="' . Yii::t('user', 'inactive') . '"></i> ' . $model->name . ' (' . $model->nickname . ')', ['class' => 'text-muted']) : Html::tag('strong', $model->name . ' (' . $model->nickname . ')'),
                            '{summary}' => $userRoles[$model->id] ?? '',
                            '{url}' => Url::to(['view', 'id' => $model->id]),
                        ]);
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width: 85px; vertical-align: middle; text-align: right'],
                    'template' => '{phone} {email}',
                    'buttons' => [
                        'phone' => function($url, $model) {
                            if (!empty($model->phone)) {
                                return Html::a('<i class="fa fa-phone"></i>', 'tel:' . $model->phone, ['class' => 'btn btn-default btn-sm', 'target' => '_blank']);
                            }
                        },
                        'email' => function($url, $model) {
                            if (!empty($model->email)) {
                                return Html::a('<i class="fa fa-envelope"></i>', 'mailto:' . $model->email, ['class' => 'btn btn-default btn-sm', 'target' => '_blank']);
                            }
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>


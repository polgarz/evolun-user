<?php

namespace evolun\user\modules\event\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;

class DefaultController extends Controller
{
    /**
     * Az felhasználó modelje
     * @var User
     */
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function init() : void
    {
        $this->setUser($this->module->getUser());
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() : array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'roles'   => ['showEvents'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Rendereli a base view-t
     * @param  int $id A felhasználó id-ja
     * @return string
     */
    public function actionIndex(int $id) : string
    {
        $event = $this->module->eventModelClass;

        $dataProvider = new ActiveDataProvider([
            'query' => $event::find()
                ->joinWith('participates')
                ->where(['user_id' => $id])
                ->orderBy('start DESC'),
            'pagination' => false,
        ]);

        if ($count = count($dataProvider->models)) {
            $this->module->title = $this->module->title . ' (' . $count . ')';
        }

        return $this->renderPartial('index', [
            'model' => $this->getUser(),
            'dataProvider' => $dataProvider,
        ]);
    }

    private function setUser($user) : void
    {
        $this->_user = $user;
    }

    private function getUser()
    {
        return $this->_user;
    }
}

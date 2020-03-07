<?php

namespace evolun\user\modules\profile\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-own-profile' => ['POST'],
                    'delete-own-profile-image' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'delete-own-profile', 'delete-own-profile-image'],
                        'allow'   => true,
                        'roles'   => ['@'],
                        'matchCallback' => function () {
                            return $this->getUser()->id == Yii::$app->user->id;
                        },
                    ],
                ]
            ],
        ];
    }

    /**
     * Rendereli a base view-t
     * @param  int $id Az esemény id-ja
     * @return string
     */
    public function actionIndex(int $id) : string
    {
        $model = $this->getUser();
        $model->password = null;
        $model->scenario = $model::SCENARIO_PROFILE;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('user/profile', 'Profile saved'));
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('user/profile', 'Saving the profile failed'));
            }
        }

        $model->password = null;

        return $this->renderPartial('index', [
            'model' => $model,
        ]);
    }


    /**
     * Az aktuális felhasználó törlése
     * @return mixed
     */
    public function actionDeleteOwnProfile($id)
    {
        $this->getUser()->delete();

        return $this->goHome();
    }

    /**
     * Saját profilkép (!!!) törlése
     * @return mixed
     * @throws NotFoundHttpException ha nem létező felhasználó id-t kap
     */
    public function actionDeleteOwnProfileImage($id)
    {
        $model = $this->getUser();
        $model->password = null;
        $model->image = null;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('user/profile', 'Profile image deleted'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('user/profile', 'Profile image delete failed'));
        }

        return $this->redirect(['view', 'id' => Yii::$app->user->id]);
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

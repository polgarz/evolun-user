<?php

namespace evolun\user\controllers;

use Yii;
use evolun\user\models\RbacForm;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Jogosultsági körök adminsztrációjának alap controllere, egy CRUD-ot valósít meg
 */
class RbacController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'index'],
                        'allow' => true,
                        'roles' => ['managePermissions']
                    ],
                ]
            ],
        ];
    }

    /**
     * Kilistázza az összes jogosultsági kört
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ArrayDataProvider([
            'models' => Yii::$app->authManager->getRoles(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Létrehoz egy új jogosultsági kört, ha sikeres, a lista oldalra ugrik
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RbacForm();
        $manager = Yii::$app->authManager;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'A jogosultsági kör létrehozva');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', 'A jogosultsági kör létrehozása sikertelen volt.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'permissions' => $manager->getPermissions(),
        ]);
    }

    /**
     * Módosít egy jogosultsági kört
     * @param string $id a jogosultsági kör neve
     * @return mixed
     * @throws NotFoundHttpException ha a kért jogosultsági kör nem létezik
     */
    public function actionUpdate($id)
    {
        $manager = Yii::$app->authManager;
        $role = $manager->getRole($id);

        if (!$role) {
            throw NotFoundHttpException('Nincs ilyen jogosultsági kör!');
        }

        $model = new RbacForm($role);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'A jogosultsági kör sikeresen módosítva');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('danger', 'A jogosultsági kör módosítása sikertelen volt.');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'permissions' => $manager->getPermissions(),
        ]);
    }

    /**
     * Töröl egy jogosultsági kört, ha sikeres, a lista oldalra ugrik
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException ha a kért jogosultsági kör nem létezik
     */
    public function actionDelete($id)
    {
        $manager = Yii::$app->authManager;
        $model = $manager->getRole($id);

        if (!$model) {
            throw NotFoundHttpException('Nincs ilyen jogosultsági kör!');
        }

        $manager->remove($model);

        return $this->redirect(['index']);
    }
}

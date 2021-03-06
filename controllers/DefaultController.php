<?php

namespace evolun\user\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use evolun\user\modules\UserSubModule;

/**
 * Felhasználói adminsztráció alap controllere, egy CRUD-ot valósít meg
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login', 'reset-password-request', 'reset-password'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow'   => true,
                        'roles'   => ['manageUsers'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'roles'   => ['showUsers'],
                    ],
                    [
                        'actions' => ['logout', 'view'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Kilistázza a felhasználókat
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = Yii::createObject($this->module->userSearchModel);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // jogosultsagok (az authmanagerbol nem lehet lekerdezni, csak minden userhez kulon kulon)
        $userRoles = (new \yii\db\Query())
            ->select('name, user_id')
            ->from(Yii::$app->authManager->assignmentTable)
            ->leftJoin(Yii::$app->authManager->itemTable, 'name = item_name')
            ->indexBy('user_id')
            ->column();

        return $this->render($this->module->userTemplates['index'], [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Ez a profil oldal action-je, fontos, hogy az aktuális felhasználó nem
     * csak megtekinteni tudja az adatokat hanem módosítani is
     * (profilszerkesztés)
     * @param  integer $id A felhasználó id-ja
     * @return mixed
     * @throws NotFoundHttpException ha nem létező felhasználó id-t kap
     * @throws ForbiddenHttpException ha olyan felhasználó szeretné
     * megtekinteni, akinek egyébként nincs joga a felhasználók listájához (ez
     * úgy fordulhat elő, hogy a saját profilját attól még láthatja)
     */
    public function actionView($id)
    {
        if (Yii::$app->user->id != $id && !Yii::$app->user->can('showUsers')) {
            throw new ForbiddenHttpException();
        }

        $model = $this->findModel($id);
        $modules = [];

        if ($this->module->modules) {
            foreach ($this->module->modules as $id => $module) {
                $module = $this->module->getModule($id);

                if (!$module instanceof UserSubModule) {
                    continue;
                }

                try {
                    $content = $module->runAction($module->defaultRoute, Yii::$app->request->get());
                } catch (\yii\web\ForbiddenHttpException $e) {
                    Yii::info($id . ' submodule: ' . $e->getMessage(), 'user');
                    continue;
                }

                $modules[] = [
                    'title' => $module->title ?? 'Modul',
                    'content' => $content
                ];
            }
        }

        return $this->render($this->module->userTemplates['view'], [
            'model' => $model,
            'modules' => $modules,
        ]);
    }

    /**
     * Felhasználó létrehozása
     * @return mixed
     */
    public function actionCreate()
    {
        $model = Yii::createObject($this->module->userModel);
        $model->scenario = $model::SCENARIO_CREATE;
        $model->last_activity = new \yii\db\Expression('NOW()');
        $model->member_since = date('Y-m-d');

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // auth
                $auth = \Yii::$app->authManager;
                if (in_array($model->role, ArrayHelper::getColumn($auth->getChildRoles(Yii::$app->user->identity->role), 'name'))) {
                    if ($model->role) {
                        $role = $auth->getRole($model->role);
                        $auth->assign($role, $model->id);
                    }
                }

                Yii::$app->session->setFlash('success', Yii::t('user', 'Create successful'));

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('user', 'Create failed'));
            }
        }

        return $this->render($this->module->userTemplates['create'], [
            'model' => $model,
        ]);
    }

    /**
     * Felhasználó adatainak módosítása, ha sikeres, a profil oldalra ugrik
     * @param integer $id a felhasználó id-ja
     * @return mixed
     * @throws NotFoundHttpException ha nem létező felhasználó id-t kap
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = $model::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // auth allitas (ha olyan jogosultsagot akar beallitani, ami engedelyezett)
                $auth = \Yii::$app->authManager;
                if (in_array($model->role, ArrayHelper::getColumn($auth->getChildRoles(Yii::$app->user->identity->role), 'name'))) {
                    $auth->revokeAll($model->id);
                    if ($model->role) {
                        $role = $auth->getRole($model->role);
                        $auth->assign($role, $model->id);
                    }
                }

                Yii::$app->session->setFlash('success', Yii::t('user', 'Update successful'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('user', 'Update failed'));
            }
        }

        $model->password = null;
        return $this->render($this->module->userTemplates['update'], [
            'model' => $model,
        ]);
    }

    /**
     * Töröl egy felhasználót, ha sikeres, a felhasználó listára ugrik
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException ha nem létező felhasználó id-t kap
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('user', 'Delete successful'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'Delete failed'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Bejelentkezés. külön layout-ja van, mert semmit nem akarunk mutatni a nem
     * bejelentkezett felhasználóknak
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = 'login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = Yii::createObject($this->module->loginFormModel);
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render($this->module->userTemplates['login'], [
            'model' => $model,
        ]);
    }

    /**
     * Jelszo helyreállítás kérése
     * @return mixed
     */
    public function actionResetPasswordRequest()
    {
        $this->layout = 'login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = Yii::createObject($this->module->resetPasswordRequestFormModel);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('user', 'We sent you an email, please follow the instructions in it'));

                return $this->goHome();
            }
        }

        return $this->render('reset-password-request', [
            'model' => $model,
        ]);
    }

    /**
     * Visszaállítja a jelszót
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        try {
            $model = Yii::createObject($this->module->resetPasswordFormModel, [$token]);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('user', 'New password is saved!'));

            return $this->goHome();
        }

        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }

    /**
     * Kijelentkezés
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Megkeres egy felhasználót a kapott id alapján
     * @param integer $id
     * @return User a felhasználó modelje
     * @throws NotFoundHttpException ha nem létező felhasználó id-t kap
     */
    protected function findModel($id)
    {
        $userModel = Yii::createObject($this->module->userModel);

        if (($model = $userModel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}

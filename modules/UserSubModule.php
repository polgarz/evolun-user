<?php

namespace evolun\user\modules;

use Yii;
use evolun\user\models\User;
use yii\web\NotFoundHttpException;

/**
 * Felhasználókhoz tartozó al modulok fő modulja
 */
class UserSubModule extends \yii\base\Module
{
    /**
     * A modul neve (ami megjelenik a tabon is)
     * @var string
     */
    public $title;

    /**
     * Az esemény modelje
     * @var User
     */
    private $_user;

    public function getUser()
    {
        return $this->_user;
    }

    public function setUser($user)
    {
        $this->_user = $user;
    }

    public function init()
    {
        // beallitjuk a felhasználót
        if ($user = User::findOne(Yii::$app->request->get('id'))) {
            $this->setUser($user);
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        parent::init();
    }
}

<?php

namespace evolun\user\models;

use Yii;
use yii\base\Model;

/**
 * Ez a bejelentkezéshez szükséges model
 */
class LoginForm extends Model
{
    /**
     * Email cím
     * @var string
     */
    public $email;

    /**
     * Jelszó
     * @var string
     */
    public $password;

    /**
     * Emlékezzen-e?
     * @var boolean
     */
    public $rememberMe = true;

    /**
     * A user model
     * @var User
     */
    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email cím',
            'password' => 'Jelszó',
            'rememberMe' => 'Jegyezz meg',
        ];
    }

    /**
     * Ellenőrzi a jelszót (custom validator)
     * @param  string $attribute Az attribútum neve
     * @param  array $params     A validátor paraméterei
     * @return void
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Hibás email cím, vagy jelszó!');
            }
        }
    }

    /**
     * Bejelentkezteti a felhasználót
     * @return boolean
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Megkeresi a felhasználót a model email tulajdonsága alapján
     * @return User
     */
    public function getUser()
    {
        $model = Yii::createObject(Yii::$app->controller->module->userModel);

        if ($this->_user === false) {
            $this->_user = $model::find()->where(['email' => $this->email])->one();
        }

        return $this->_user;
    }
}

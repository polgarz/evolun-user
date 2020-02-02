<?php
namespace evolun\user\models;

use yii;
use yii\base\Model;
use yii\base\InvalidParamException;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $passwordRepeat;

    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Üres token!');
        }
        $userModel = Yii::createObject(Yii::$app->controller->module->userModel);
        $this->_user = $userModel::findIdentityByAccessToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Hibás token!');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'passwordRepeat'], 'required'],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => 'A két jelszónak egyeznie kell!'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Új jelszó',
            'passwordRepeat' => 'Új jelszó (újra)'
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->password = $this->password;

        return $user->save(false);
    }
}

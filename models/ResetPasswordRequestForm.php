<?php
namespace evolun\user\models;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class ResetPasswordRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => Yii::$app->controller->module->userModel,
                'targetAttribute' => ['email' => 'email'],
                'message' => 'Nincs ilyen email cím a rendszerben'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email cím',
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $userModel = Yii::$app->controller->module->userModel;
        $user = $userModel::findOne([
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        return Yii::$app->mailer->compose(Yii::$app->controller->module->resetPasswordRequestEmail, ['user' => $user])
                ->setFrom(Yii::$app->params['mainEmail'])
                ->setTo($this->email)
                ->setSubject('Elfelejtett jelszó')
                ->send();
    }
}

<?php

namespace evolun\user;

use Yii;

/**
 * Felhasználó modul
 *
 * Ez egy alap modul, a felület használatához elengedhetetlen!
 *
 * Ez a modul kezeli a felhasználókat, tartalmaz:
 * - jogosultságokat
 * - jogosultság adminisztrációt (létrehozás / törlés / módosítás)
 * - felhasználó listát
 * - felhasználó adminisztrációt (létrehozás / törlés / módosítás)
 * - felhasználói profilt (szerkesztési lehetőséggel)
 * - bejelentkezést
 * - jelszóemlékeztető funkciót
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'evolun\user\controllers';

    /**
     * Felhasználók modelje
     * @var string
     */
    public $userModel = 'evolun\user\models\User';

    /**
     * Felhasználók kereséséhez szükséges model
     * @var string
     */
    public $userSearchModel = 'evolun\user\models\UserSearch';

    /**
     * A bejelentkezéshez szükséges model
     * @var string
     */
    public $loginFormModel = 'evolun\user\models\LoginForm';

    /**
     * A model, amivel jelszóemlékeztetőt lehet kérni email-re
     * @var string
     */
    public $resetPasswordRequestFormModel = 'evolun\user\models\ResetPasswordRequestForm';

    /**
     * A model, amivel a konkrét jelszó visszaállítás történik
     * @var string
     */
    public $resetPasswordFormModel = 'evolun\user\models\ResetPasswordForm';

    /**
     * A custom template fajlok
     * @var array
     */
    public $userTemplates = [];

    /**
     * A felhasználó profil bal oldalán megjelenő dobozok (a fő doboz alatt)
     * @var array
     */
    public $widgets = [];

    /**
     * A regisztrációs email
     * @var string
     */
    public $registerEmail = '@vendor/polgarz/evolun-user/mail/{language}/register';

    /**
     * Az email, amivel a jelszót lehet visszaállítani
     * @var string
     */
    public $resetPasswordRequestEmail = '@vendor/polgarz/evolun-user/mail/{language}/reset-password-request';

    /**
     * Az alap template fajlok
     * @var array
     */
    private $_defaultUserTemplates = [
        'index' => 'index',
        'view' => 'view',
        'tools' => '_tools',
        'form' => '_form',
        'update' => 'update',
        'create' => 'create',
        'login' => 'login',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();

        $this->registerEmail = strtr($this->registerEmail, ['{language}' => Yii::$app->language]);
        $this->resetPasswordRequestEmail = strtr($this->resetPasswordRequestEmail, ['{language}' => Yii::$app->language]);

        if (!empty($this->userTemplates)) {
            $this->userTemplates = array_merge($this->_defaultUserTemplates, $this->userTemplates);
        } else {
            $this->userTemplates = $this->_defaultUserTemplates;
        }
    }

    public function registerTranslations()
    {
        if (!isset(Yii::$app->get('i18n')->translations['user'])) {
            Yii::$app->get('i18n')->translations['user*'] = [
                'class' => \yii\i18n\PhpMessageSource::className(),
                'basePath' => __DIR__ . '/messages',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'user' => 'user.php',
                ]
            ];
        }
    }
}

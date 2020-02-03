<?php

namespace evolun\user\modules\profile;

use Yii;

/**
 * Felhasználói profil modul (speciális, csak az adott felhasználónak jelenik meg (lsd user DefaultController))
 */
class Module extends \evolun\user\modules\UserSubModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'evolun\user\modules\profile\controllers';

    /**
     * Az felhasználók modelje
     * @var string
     */
    public $userModelClass = 'evolun\user\models\User';

    public $title;

    public function init()
    {
        parent::init();

        if (!$this->title) {
            $this->title = Yii::t('user', 'Settings');
        }
    }
}

<?php

namespace evolun\user\modules\event;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Felhasználókhoz tartozó események modulja
 */
class Module extends \evolun\user\modules\UserSubModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'evolun\user\modules\event\controllers';

    /**
     * A felhasználók modelje
     * @var string
     */
    public $userModelClass = 'evolun\user\models\User';

    /**
     * Az esemenyek modelje
     * @var string
     */
    public $eventModelClass = 'evolun\event\models\Event';

    /**
     * Az esemenyek asset bundleje (kell az ikonokhoz)
     * @var string
     */
    public $eventAssetBundle = 'evolun\event\assets\EventAsset';

    /**
     * @var string
     */
    public $eventModuleId = 'event';

    public function init()
    {
        if (!class_exists($this->eventModelClass)) {
            throw new InvalidConfigException(Yii::t('user', 'Event model class not found!'));
        }

        if (!class_exists($this->eventAssetBundle)) {
            throw new InvalidConfigException(Yii::t('user', 'Event asset bundle class not found!'));
        }

        if (Yii::$app->hasModule($this->eventModuleId)) {
            Yii::$app->getModule($this->eventModuleId);
        }

        if (!$this->title) {
            $this->title = Yii::t('user', 'Events');
        }

        parent::init();
    }
}

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
    public $eventModelClass = 'app\modules\event\models\Event';

    /**
     * Az esemenyek asset bundleje (kell az ikonokhoz)
     * @var string
     */
    public $eventAssetBundle = 'app\modules\event\assets\EventAsset';

    public function init()
    {
        if (!class_exists($this->eventModelClass)) {
            throw new InvalidConfigException('Az eseményekhez tartozó model nem található!');
        }

        if (!class_exists($this->eventAssetBundle)) {
            throw new InvalidConfigException('Az eseményekhez tartozó asset bundle nem található!');
        }

        parent::init();
    }
}

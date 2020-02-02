<?php
namespace evolun\user\widgets;

use Yii;

/**
 * A layout jobb felső sarkában található profil dropdown widgetje
 */
class ProfileDropdown extends \yii\base\Widget implements UserWidgetInterface
{
    /**
     * A felhasználó, akinek az adatait meg kell jeleníteni. Ha nincs megadva,
     * akkor az aktuális felhasználó adatait használja
     * @var User
     */
    public $user = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!$this->user) {
            $this->user = Yii::$app->user->identity;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('profile-dropdown', [
            'user' => $this->user,
            ]);
    }
}

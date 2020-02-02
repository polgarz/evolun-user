<?php
namespace evolun\user\widgets;

use Yii;
use evolun\user\models\User;

/**
 * A legutobb csatlakozott felhasználók listája
 */
class RecentUsers extends \yii\base\Widget
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!Yii::$app->user->can('showUsers')) {
            return null;
        }

        $users = User::find()
            ->where(['is not', 'member_since', null])
            ->orderBy('member_since DESC')
            ->limit(8)
            ->all();

        return $this->render('recent-users', [
            'users' => $users,
            ]);
    }
}

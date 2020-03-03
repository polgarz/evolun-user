<?php
namespace evolun\user\widgets;

use Yii;

class Responsible extends \yii\base\Widget implements UserWidgetInterface
{
    /**
     * @var User
     */
    public $user = false;

    /**
     * @var string
     */
    public $userModuleId = 'user';

    /**
     * @var string
     */
    public $kidModuleId = 'kid';

    /**
     * @var string
     */
    public $responsibleUserModel = 'evolun\kid\models\ResponsibleUser';

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
        if (!Yii::$app->user->can('showKids')) {
            return null;
        }

        if (!Yii::$app->hasModule($this->kidModuleId)) {
            throw new \yii\base\InvalidConfigException('You have to install \'evolun-kid\' to use this widget');
        }

        $responsibleUsers = $this->responsibleUserModel::find()
            ->where(['user_id' => $this->getUser()->id])
            ->all();

        return $this->render('responsible', [
            'responsibleUsers' => $responsibleUsers,
            ]);
    }
}

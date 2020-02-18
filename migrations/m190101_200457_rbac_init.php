<?php

use yii\db\Migration;

/**
 * Class m190101_200457_rbac_init
 */
class m190101_200457_rbac_init extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        $showUsers = $auth->createPermission('showUsers');
        $showUsers->description = 'Show volunteers data';
        $auth->add($showUsers);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Add, edit, or delete volunteers';
        $auth->add($manageUsers);

        $managePermissions = $auth->createPermission('managePermissions');
        $managePermissions->description = 'Manage permissions';
        $auth->add($managePermissions);

        $admin = $auth->createRole('admin');
        $admin->description = 'Admin';
        $auth->add($admin);

        $auth->addChild($admin, $showUsers);
        $auth->addChild($admin, $manageUsers);
        $auth->addChild($admin, $managePermissions);

        $auth->assign($admin, 1);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }
}

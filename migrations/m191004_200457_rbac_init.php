<?php

use yii\db\Migration;

/**
 * Class m191004_200457_rbac_init
 */
class m191004_200457_rbac_init extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        $showUsers = $auth->createPermission('showUsers');
        $showUsers->description = 'Megtekintheti az önkéntesek adatait';
        $auth->add($showUsers);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Hozzáadhat, törölhet, módosíthat önkénteseket';
        $auth->add($manageUsers);

        $managePermissions = $auth->createPermission('managePermissions');
        $managePermissions->description = 'Módosíthatja a jogosultsági szinteket';
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

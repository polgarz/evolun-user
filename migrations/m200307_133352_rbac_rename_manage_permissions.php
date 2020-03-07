<?php

use yii\db\Migration;

/**
 * Class m200307_133352_rbac_rename_manage_permissions
 */
class m200307_133352_rbac_rename_manage_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $manageAdminData = $auth->createPermission('manageAdminData');
        $manageAdminData->description = 'Can manage admin data (eg. user permissions)';

        $auth->update('managePermissions', $manageAdminData);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $managePermissions = $auth->createPermission('managePermissions');
        $managePermissions->description = 'Manage permissions';

        $auth->update('manageAdminData', $managePermissions);
    }
}

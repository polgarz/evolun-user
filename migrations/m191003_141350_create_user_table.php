<?php

use yii\db\Migration;

/**
 * Class m191003_141350_create_user_table
 */
class m191003_141350_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id'              => $this->primaryKey(),
            'name'            => $this->string()->notNull(),
            'nickname'        => $this->string(),
            'email'           => $this->string()->notNull(),
            'password'        => $this->string(),
            'phone'           => $this->string()->notNull(),
            'member_since'    => $this->date(),
            'birth_date'      => $this->date(),
            'skype'           => $this->string(),
            'facebook'        => $this->string(),
            'address'         => $this->string(),
            'driving_license' => $this->boolean(),
            'auth_key'        => $this->string()->notNull(),
            'inactive'        => $this->boolean(),
            'last_activity'   => $this->datetime(),
            'image'           => $this->string(),
            'created_at'      => $this->datetime(),
            'updated_at'      => $this->datetime(),
            'created_by'      => $this->integer(),
            'updated_by'      => $this->integer(),
            ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->addForeignKey('fk_user_created_by_user_id', '{{%user}}', 'created_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_user_updated_by_user_id', '{{%user}}', 'updated_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        $this->insert('user', [
            'email' => 'admin@admin.com',
            'password' => md5('admin'),
            'phone' => '000',
            'nickname' => 'Admin',
            'auth_key' => md5('admin'),
            'name' => 'Admin'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}

<?php

use yii\db\Schema;
use yii\db\Migration;

class m160403_102510_settings_change_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('settings', [
            'id'          => Schema::TYPE_PK,

            'user_id'     => Schema::TYPE_BIGINT.' NOT NULL',

            'type'        => Schema::TYPE_INTEGER.' NOT NULL',
            'json_data'   => 'text NOT NULL',

            'is_confirm' => Schema::TYPE_BOOLEAN.' DEFAULT false NOT NULL',

            'created_at'  => 'int8 NOT NULL',
            'updated_at'  => 'int8 NOT NULL',
            'deleted_at'  => 'int8 DEFAULT NULL',

            'is_deleted'  => Schema::TYPE_BOOLEAN.' DEFAULT false NOT NULL',
        ]);

        $this->createIndex('idx-settings-user-type', 'settings', 'user_id, type, is_deleted');
        $this->createIndex('idx-settings-user-type-is_confirm', 'settings', 'user_id, type, is_confirm, is_deleted');
        $this->createIndex('idx-settings-is_deleted', 'settings', 'is_deleted');
    }

    public function down()
    {
        $this->dropTable('settings');
    }
}

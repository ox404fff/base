<?php

use yii\db\Schema;
use yii\db\Migration;

class m160403_102450_confirm_codes_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('confirm_codes', [
            'id'          => Schema::TYPE_PK,

            'user_id'      => Schema::TYPE_BIGINT,

            'type'         => Schema::TYPE_INTEGER.' NOT NULL',
            'confirm_code' => 'varchar(64) NOT NULL',

            'created_at'  => 'int8 NOT NULL',
            'updated_at'  => 'int8 NOT NULL',
            'deleted_at'  => 'int8 DEFAULT NULL',

            'is_deleted'  => Schema::TYPE_BOOLEAN.' DEFAULT false NOT NULL',
        ]);

        $this->createIndex('idx-confirm_codes-type-confirm_code', 'confirm_codes', 'type, confirm_code, is_deleted');
        $this->createIndex('idx-confirm_codes-user-type-confirm_code', 'confirm_codes', 'user_id, type, confirm_code, is_deleted');
        $this->createIndex('idx-confirm_codes-is_deleted', 'confirm_codes', 'is_deleted');
    }

    public function down()
    {
        $this->dropTable('confirm_codes');
    }
}

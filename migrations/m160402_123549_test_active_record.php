<?php

use yii\db\Schema;
use yii\db\Migration;

class m160402_123549_test_active_record extends Migration
{
    public function safeUp()
    {
        if (YII_ENV != 'test') {
            return true;
        }

        $this->createTable('active_record_test', [
            'id'          => Schema::TYPE_PK,

            'number'      => 'int4 NOT NULL',
            'text'        => 'text DEFAULT NULL',

            'created_at'  => 'int8 NOT NULL',
            'updated_at'  => 'int8 NOT NULL',

            'is_deleted'  => Schema::TYPE_BOOLEAN.' DEFAULT false NOT NULL',
            'deleted_at'  => 'int8 DEFAULT NULL',
        ]);

        return true;
    }

    public function down()
    {
        if (YII_ENV != 'test') {
            return true;
        }
        $this->dropTable('active_record_test');
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

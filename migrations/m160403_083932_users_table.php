<?php

use yii\db\Schema;
use yii\db\Migration;
use test\fixtures\UserFixture;

class m160403_083932_users_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('users', [
            'id'        => Schema::TYPE_PK,
            'type'      => 'int4 DEFAULT 0 NOT NULL',
            'login'     => 'varchar(128) NOT NULL',
            'password'  => 'varchar(64) NOT NULL',
            'auth_key'  => 'varchar(64) NOT NULL',

            'created_at'  => 'int8 NOT NULL',
            'updated_at'  => 'int8 NOT NULL',
            'deleted_at'  => 'int8 DEFAULT NULL',

            'is_deleted'  => Schema::TYPE_BOOLEAN.' DEFAULT false NOT NULL',
        ]);
        $this->createIndex('idx-users-login-type', 'users', 'login, type, is_deleted');
        $this->createIndex('idx-users-is_deleted', 'users', 'is_deleted');

        if (YII_ENV != 'test') {
            $password = \Yii::$app->security->generateRandomString(16);
        } else {
            $password = UserFixture::getPassword(UserFixture::ID_USER_ADMIN);
        }

        echo 'Created admin user width password '. $password.PHP_EOL;

        $adminUser = app\models\User::createUser(\Yii::$app->params['adminEmail'], \Yii::$app->getSecurity()->generatePasswordHash($password), app\models\User::TYPE_USER_ADMIN);

        if (!$adminUser->save()) {
            return false;
        }

        $auth = \Yii::$app->getAuthManager();

        $auth->removeAllAssignments();

        return true;
    }

    public function down()
    {
        $this->dropTable('users');
    }

}

INSTALLATION
------------

php yii migrate --migrationPath=@yii/rbac/migrations
php tests/codeception/bin/yii migrate --migrationPath=@yii/rbac/migrations
php yii access-manager/init
php tests/codeception/bin/yii access-manager/init
php yii migrate/up
php tests/codeception/bin/yii migrate/up
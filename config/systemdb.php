<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.getenv('DATABASE_SERVER').';dbname=doitincloud_db',
    'username' => getenv('DB_USER_NAME'),
    'password' => getenv('DB_USER_PASS'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

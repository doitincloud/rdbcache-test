<?php

return [
    'class' => 'yii\redis\Connection',
    'hostname' => getenv('REDIS_SERVER'),
    'port' => 6379,
    'database' => 0,
];

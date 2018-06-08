<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\Inflector;
use yii\console\Controller;

class TestDbController extends Controller
{
    public function actionIndex()
    {
        try {
            $conn = Yii::$app->datadb;
            $tables = $conn->schema->getTableNames();
            foreach($tables as $table) {
                var_dump($table);
            }
        } catch (\yii\db\Exception $e) {
            echo "failed to connect to database";
            echo $e->code;
            exit(1);
        }
    }
}

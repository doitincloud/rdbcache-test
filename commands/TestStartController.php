<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestStartController extends TestController
{
    public function actionIndex()
    {
        $tables_needed = array(
            //"rdbcache_kv_pair", "rdbcache_monitor", "rdbcache_client_test", "rdbcache_stopwatch", 
            "departments", "employees", "dept_emp", "dept_manager", "titles", "salaries", 
            "departments2", "employees2", "dept_emp2", "dept_manager2", "titles", "salaries2", 
            "tb1", "tb2", "user_table");
        try {
            $conn = Yii::$app->datadb;
            $tables = $conn->schema->getTableNames();
            $missing_tables = "";
            foreach ($tables_needed as $table_needed) {
                echo $tables_needed . "\n";
                $hasIt = false;
                foreach($tables as $table) {
                    if ($table == $table_needed) {
                        $hasIt = true;
                        break;
                    }
                }
                if (!$hasIt) {
                    $missing_tables .= " " . $table_needed;
                }
            }
            if (!empty($missing_tables)) {
                echo "mssing needed table(s): ".$missing_tables;
                exit(1);
            }
        } catch (\yii\db\Exception $e) {
            echo "failed to connect to database";
            echo $e->errorMessage();
            exit(1);
        }

        echo $this->getTestStartAtId();
    }
}

<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestSaveController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionWithTable();

        $this->actionWithTableAndSync();

        $this->actionWithTableAndGeneratedId();

        $this->actionWithTableAndGeneratedIdAndSync();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionWithTable() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';

        $values_array = [
            ['name' => 'new_name001', 'age' => 11],
            ['name' => 'new_name002', 'age' => 12],
            ['name' => 'new_name003', 'age' => 13],
        ];
        
         //insert
        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($values_array)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $this->HttpOK($response);

        echo "\nwait 1 seconds for sync data\n";
        sleep(1);
        
        // check database
        //
        for ($i = 0; $i < count($values_array); $i++) {

            $condition = $values_array[$i];
            $value = $this->dbFind($condition, $table);

            if ($value == null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . json_encode($condition));
                return;
            }
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndSync() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';

        $values_array = [
            ['name' => 'new_name001', 'age' => 11],
            ['name' => 'new_name002', 'age' => 12],
            ['name' => 'new_name003', 'age' => 13],
        ];
        
         //insert
        $api = '/rdbcache/v1/save/'.$table.'/sync';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($values_array)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        for ($i = 0; $i < count($values_array); $i++) {

            $condition = $values_array[$i];
            $value = $this->dbFind($condition, $table);

            if ($value == null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . json_encode($condition));
                return;
            }
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndGeneratedId() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';

        $values_array = [
            ['name' => 'new_name001', 'age' => 11 ],
            ['name' => 'new_name002', 'age' => 12 ],
            ['name' => 'new_name003', 'age' => 13 ],
        ];
        
         //insert
        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($values_array)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $this->HttpOK($response);

        echo "\nwait 1 seconds for sync data\n";
        sleep(1);

        // check database
        //
        for ($i = 0; $i < count($values_array); $i++) {

            $value = $this->dbFind($values_array[$i], $table);

            if ($value == null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . json_encode($values_array[$i]));
                return;
            }
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndGeneratedIdAndSync() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';

        $values_array = [
            ['name' => 'new_name001', 'age' => 11 ],
            ['name' => 'new_name002', 'age' => 12 ],
            ['name' => 'new_name003', 'age' => 13 ],
        ];
        
         //insert
        $api = '/rdbcache/v1/save/'.$table.'/sync';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($values_array)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        for ($i = 0; $i < count($values_array); $i++) {

            $value = $this->dbFind($values_array[$i], $table);

            if ($value == null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . json_encode($values_array[$i]));
                return;
            }
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}

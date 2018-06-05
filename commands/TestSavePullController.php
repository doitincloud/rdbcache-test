<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestSavePullController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionDepartments();

        $this->actionEmployees();

        $this->actionSalaries();

        $this->actionTitles();

        $this->actionDeptEmp();

        $this->actionDeptManager();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionDepartments() {

        $this->setup(__FUNCTION__);

        $this->empdb_init(false, false);
        
        $table = 'departments';
        $limit = 1024;

        // get expected from database
        //
        $input_array = $this->dbFindAll($table, $limit);

        if ($input_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $size = count($input_array);

        $this->DatabaseOK();

        $table .= '2';

        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($input_array)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $keys = $response->data['data'];
        if ( count($keys) != $size) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        for ($i = 0; $i < $size; $i++) {

            $key = $keys[$i];
            $expected = $input_array[$i];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/rdbcache/v1/pull/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        for ($i = 0; $i < $size; $i++) {
            $key = $keys[$i];
            $expected = $input_array[$i];
            $value = $data[$key];
            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }

        $this->HttpOK($response);

        // wait for database insert operations
        echo "\nwait for 1 second\n"; sleep(1);

        $data = $this->dbWaitFindAll($table, $size);

        if ($data != $input_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionEmployees() {

        $this->setup(__FUNCTION__);

        $this->empdb_init(true, false);

        $table = 'employees';
        $limit = 1024;

        // get expected from database
        //
        $input_array = $this->dbFindAll($table, $limit);

        if ($input_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $size = count($input_array);
        
        $this->DatabaseOK();

        $table .= '2';

        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($input_array)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $keys = $response->data['data'];
        if ( count($keys) != $size) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        for ($i = 0; $i < $size; $i++) {

            $key = $keys[$i];
            $expected = $input_array[$i];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/rdbcache/v1/pull/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        for ($i = 0; $i < $size; $i++) {
            $key = $keys[$i];
            $expected = $input_array[$i];
            $value = $data[$key];
            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }
        $this->HttpOK($response);

        // wait for database insert operations
        sleep(1);

        $data = $this->dbWaitFindAll($table, $size);

        if ($data != $input_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSalaries() {

        $this->setup(__FUNCTION__);

        $this->empdb_init(true, true);

        $table = 'salaries';
        $limit = 1024;

        // get expected from database
        //
        $input_array = $this->dbFindAll($table, $limit);

        if ($input_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $size = count($input_array);
        
        $this->DatabaseOK();

        $table .= '2';

        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($input_array)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $keys = $response->data['data'];
        if ( count($keys) != $size) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        for ($i = 0; $i < $size; $i++) {

            $key = $keys[$i];
            $expected = $input_array[$i];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/rdbcache/v1/pull/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        for ($i = 0; $i < $size; $i++) {
            $key = $keys[$i];
            $expected = $input_array[$i];
            $value = $data[$key];
            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }
        $this->HttpOK($response);

        // wait for database insert operations
        sleep(1);

        $data = $this->dbWaitFindAll($table, $size);

        if ($data != $input_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionTitles() {

        $this->setup(__FUNCTION__);

        $this->empdb_init(true, true);

        $table = 'titles';
        $limit = 1024;

        // get expected from database
        //
        $input_array = $this->dbFindAll($table, $limit);

        if ($input_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $size = count($input_array);
        
        $this->DatabaseOK();

        $table .= '2';

        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($input_array)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $keys = $response->data['data'];
        if ( count($keys) != $size) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        for ($i = 0; $i < $size; $i++) {

            $key = $keys[$i];
            $expected = $input_array[$i];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/rdbcache/v1/pull/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        for ($i = 0; $i < $size; $i++) {
            $key = $keys[$i];
            $expected = $input_array[$i];
            $value = $data[$key];
            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }
        $this->HttpOK($response);

        // wait for database insert operations
        sleep(1);

        $data = $this->dbWaitFindAll($table, $size);

        if ($data != $input_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptManager() {

        $this->setup(__FUNCTION__);

        $this->empdb_init(true, true);

        $table = 'dept_manager';
        $limit = 1024;

        // get expected from database
        //
        $input_array = $this->dbFindAll($table, $limit);

        if ($input_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $size = count($input_array);
        
        $this->DatabaseOK();

        $table .= '2';

        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($input_array)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $keys = $response->data['data'];
        if ( count($keys) != $size) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        for ($i = 0; $i < $size; $i++) {

            $key = $keys[$i];
            $expected = $input_array[$i];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/rdbcache/v1/pull/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        for ($i = 0; $i < $size; $i++) {
            $key = $keys[$i];
            $expected = $input_array[$i];
            $value = $data[$key];
            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }
        $this->HttpOK($response);

        // wait for database insert operations
        sleep(1);

        $data = $this->dbWaitFindAll($table, $size);

        if ($data != $input_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptEmp() {

        $this->setup(__FUNCTION__);

        $this->empdb_init(true, true);

        $table = 'dept_emp';
        $limit = 1024;

        // get expected from database
        //
        $input_array = $this->dbFindAll($table, $limit);

        if ($input_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $size = count($input_array);
        
        $this->DatabaseOK();

        $table .= '2';

        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($input_array)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $keys = $response->data['data'];
        if ( count($keys) != $size) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        for ($i = 0; $i < $size; $i++) {

            $key = $keys[$i];
            $expected = $input_array[$i];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/rdbcache/v1/pull/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        for ($i = 0; $i < $size; $i++) {
            $key = $keys[$i];
            $expected = $input_array[$i];
            $value = $data[$key];
            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }
        $this->HttpOK($response);

        // wait for database insert operations
        sleep(1);

        $data = $this->dbWaitFindAll($table, $size);

        if ($data != $input_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}

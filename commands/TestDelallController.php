<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestDelallController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionSimpleWithSync();

        $this->actionWithTable();

        $this->actionWithTableAndSync();

        $this->actionWithPost();

        $this->actionWithPostAndSync();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionSimple() {

        $this->setup(__FUNCTION__);

        // get value from database
        //
        $key = 'id1';

        $expected_value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($expected_value == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/rdbcache/v1/get/'.$key;
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $data = $response->data['data'];
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, null, $expected_value);

        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // delkey
        //
        $api = '/rdbcache/v1/delall/'.$key;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        if (!empty($response->data['data'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, null, null);

        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        echo  "wait 1 second\n";
        sleep(1);

        $value = $this->dbWaitFind(['id' => $key, 'type' => 'data'], null, false);

        if ($value != null) {
            $this->failed("data incorrect in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithSync() {

        $this->setup(__FUNCTION__);

        // get value from database
        //
        $key = 'id1';

        $expected_value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($expected_value == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/rdbcache/v1/get/'.$key.'/sync';
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $data = $response->data['data'];
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, null, $expected_value);

        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // delkey
        //
        $api = '/rdbcache/v1/delall/'.$key.'/sync';
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        if (!empty($response->data['data'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, null, null);

        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        echo  "wait 1 second\n";
        sleep(1);

        $value = $this->dbWaitFind(['id' => $key, 'type' => 'data'], null, false);

        if ($value != null) {
            $this->failed("data incorrect in database");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable() {

        $this->setup(__FUNCTION__);

        // get expected from database
        //
        $expected_array = $this->dbFind(1, 'tb1');

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $key = 'my-hash-key-delall';
        $api = '/rdbcache/v1/get/'.$key.'/tb1?id=1';
        
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }
        
        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, 'tb1', $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // delkey
        //
        $api = '/rdbcache/v1/delall/'.$key.'/tb1';
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        if (!empty($response->data['data'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, 'tb1', null);

        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        echo  "wait 1 second\n";
        sleep(1);

        $array = $this->dbWaitFind(1, 'tb1', false);

        if ($array != null) {
            $this->failed("data incorrect in database");
            return;
        }

        $this->DatabaseOK();
        
        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndSync() {

        $this->setup(__FUNCTION__);

        // get expected from database
        //
        $expected_array = $this->dbFind(1, 'tb1');

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $key = 'my-hash-key-delall';
        $api = '/rdbcache/v1/get/'.$key.'/tb1/sync?id=1';
        
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }
        
        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, 'tb1', $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // delkey
        //
        $api = '/rdbcache/v1/delall/'.$key.'/tb1/sync';
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        if (!empty($response->data['data'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, 'tb1', null);

        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        echo  "wait 1 second\n";
        sleep(1);

        $array = $this->dbWaitFind(1, 'tb1', false);

        if ($array != null) {
            $this->failed("data incorrect in database");
            return;
        }

        $this->DatabaseOK();
        
        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
    
    public function actionWithPost() {

        $this->setup(__FUNCTION__);

        // get expected from database
        //
        $expected_array = $this->dbFind(['id' => 1, 'id' => 2, 'id' => 3], 'tb1');

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/rdbcache/v1/select/tb1?id=1&id=2&id=3';
        
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }
        
        $data = $response->data['data'];
        if (count($data) != 3) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $keys = array_keys($data);

        $this->HttpOK($response);

        // check redis
        //
        foreach ($data as $key => $expected_array) {

            $data = $this->waitForRedisHashUpdate($key, 'tb1', $expected_array);

            if ($data != $expected_array) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // delkey
        //
        $api = '/rdbcache/v1/delall/tb1';
        $response = $this->createRequest(true)
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
        if ($data != $keys) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        foreach ($keys as $key) {

            $data = $this->waitForRedisHashUpdate($key, 'tb1', null);

            if ($data != null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        echo  "wait 1 second\n";
        sleep(1);

        $array = $this->dbWaitFind(['id' => 1, 'id' => 2, 'id' => 3], 'tb1', false);

        if ($array != null) {
            $this->failed("data incorrect in database");
            return;
        }

        $this->DatabaseOK();
        
        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithPostAndSync() {

        $this->setup(__FUNCTION__);

        // get expected from database
        //
        $expected_array = $this->dbFind(['id' => 1, 'id' => 2, 'id' => 3], 'tb1');

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/rdbcache/v1/select/tb1/sync?id=1&id=2&id=3';
        
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }
        
        $data = $response->data['data'];
        if (count($data) != 3) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $keys = array_keys($data);

        $this->HttpOK($response);

        // check redis
        //
        foreach ($data as $key => $expected_array) {

            $data = $this->waitForRedisHashUpdate($key, 'tb1', $expected_array);

            if ($data != $expected_array) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // delkey
        //
        $api = '/rdbcache/v1/delall/sync/tb1';
        $response = $this->createRequest(true)
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
        if ($data != $keys) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        foreach ($keys as $key) {

            $data = $this->waitForRedisHashUpdate($key, 'tb1', null);

            if ($data != null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        echo  "wait 1 second\n";
        sleep(1);

        $array = $this->dbWaitFind(['id' => 1, 'id' => 2, 'id' => 3], 'tb1', false);

        if ($array != null) {
            $this->failed("data incorrect in database");
            return;
        }

        $this->DatabaseOK();
        
        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}

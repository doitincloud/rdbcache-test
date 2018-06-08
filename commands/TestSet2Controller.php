<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestSet2Controller extends TestController
{

    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimpleWithExpire();

        $this->actionSimpleWithGeneratedIdAndExpire();

        $this->actionWithTableAndExpire();

        $this->actionWithTableAndGeneratedIdAndExpire();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionSimpleWithExpire() {

        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = 'my-key-expire';
        $value = 'my-value-expire';
        $expected_value = $value;
        $table = null;
        
        $api = '/rdbcache/v1/set/'.$key.'/'.$value.'/'.$expire;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }
        
        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_value);
        
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
        $api = '/rdbcache/v1/get/'.$key;
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
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

        // check database
        //
        $value = $this->dbWaitFind(['id' => $key, 'type' => 'data']);
        
        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
            return;
        }

        $this->DatabaseOK();

        // wait for expiration
        echo "\nsleep $expire seconds\n"; sleep($expire);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, null);

        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithGeneratedIdAndExpire() {

        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = '*';
        $value = 'my-value';
        $expected_value = $value;
        $table = null;

        $api = '/rdbcache/v1/set/'.$key.'/'.$value.'/'.$expire;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }
        
        $resp_key = $response->data['key'];
        if ($resp_key == $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $key = $resp_key;

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_value);

        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
        $api = '/rdbcache/v1/get/'.$key;
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
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

        // check database
        //
        $value = $this->dbWaitFind(['id' => $key, 'type' => 'data']);
        
        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
            return;
        }

        $this->DatabaseOK();

        // wait for expiration
        echo "\nsleep $expire seconds\n"; sleep($expire);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, null);

        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndExpire()
    {

        $this->setup(__FUNCTION__);

        $expire = 5;
        $key = 'my-key-expire-123';
        $value = ["name" => "mike", "age" => "20"];
        $expected_array = $value;
        $table = 'tb1';

        $api = '/rdbcache/v1/set/'.$key.'/'.$table.'/'.$expire;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $this->HttpOK($response);

        echo "wait for 3 seconds\n"; sleep(3);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, 'data', 'id');

        if (empty($data)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not found");
            return;
        }
        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, missing id in redis data ");
            return;
        }
        unset($data['id']);
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
        $api = '/rdbcache/v1/get/'.$key.'/'.$table;
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $data = $response->data['data'];

        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data missing id");
            return;
        }
        $id = $data['id'];
        unset($data['id']);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $record = $this->dbWaitFind($id, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }

        unset($record['id']);

        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        // wait for exipration
        echo "\nsleep 3 seconds\n"; sleep(3);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, null);

        if (!empty($data)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not removed");
            return;
        }

        $this-> RedisOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndGeneratedIdAndExpire()
    {

        $this->setup(__FUNCTION__);

        $key = '*';
        $value = ["name" => "mike", "age" => "20"];
        $expected_array = $value;
        $table = 'tb1';
        $expire = 3;

        $api = '/rdbcache/v1/set/'.$key.'/'.$table.'/'.$expire;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $resp_key = $response->data['key'];

        $this->HttpOK($response);

        $key = $resp_key;

        echo "sleep 1 seconds\n"; sleep(1);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, 'data', 'id');

        if (empty($data)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not found");
            return;
        }
        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, missing id in redis data ");
            return;
        }
        unset($data['id']);
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
        $api = '/rdbcache/v1/get/'.$key.'/'.$table;
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $data = $response->data['data'];

        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data missing id");
            return;
        }
        $id = $data['id'];
        unset($data['id']);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $record = $this->dbWaitFind($id, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }

        unset($record['id']);

        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        // wait for expiration
        echo "\nsleep 2 seconds\n"; sleep(2);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, null);

        if (!empty($data)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not found");
            return;
        }

        $this-> RedisOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

}
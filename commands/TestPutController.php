<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestPutController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionSimpleWithSync();

        $this->actionSimpleWithJsonValue();

        $this->actionSimpleWithJsonValueAndSync();

        $this->actionSimpleWithJsonValueAndExpire();

        $this->actionSimpleWithJsonValueAndExpireAndSync();

        $this->actionWithTable();

        $this->actionWithTableAndSync();

        $this->actionWithTableAndExpire();

        $this->actionWithTableAndExpireAndSync();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionSimple()
    {
        $this->setup(__FUNCTION__);

        $key = 'id1';
        $value = 'value3';
        $expected_value = 'value3';
        $table = null;

        $api = '/rdbcache/v1/put/'.$key;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('put')
            ->setApi($api)
            ->setData($value)
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
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Not found from database");
            return;
        }

        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": database data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithSync()
    {
        $this->setup(__FUNCTION__);

        $key = 'id1';
        $value = 'value3';
        $expected_value = 'value3';
        $table = null;

        $api = '/rdbcache/v1/put/'.$key.'/sync';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('put')
            ->setApi($api)
            ->setData($value)
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

        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Not found from database");
            return;
        }

        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": database data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
    
    // put a partial json value use default table
    //
    public function actionSimpleWithJsonValue()
    {
        $this->setup(__FUNCTION__);

        $key = 'id4';
        $value = ["f3" => "v333", "f4" => 123];
        $expected_array = ["f1"=>"v41","f2"=>"v42","f3"=>"v333","f4"=>123];
        $table = null;

        $api = '/rdbcache/v1/put/'.$key;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('put')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
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
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Not found from database");
            return;
        }

        if ($value != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": database data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    // put a partial json value use default table
    //
    public function actionSimpleWithJsonValueAndSync()
    {
        $this->setup(__FUNCTION__);

        $key = 'id4';
        $value = ["f3" => "v333", "f4" => 123];
        $expected_array = ["f1"=>"v41","f2"=>"v42","f3"=>"v333","f4"=>123];
        $table = null;

        $api = '/rdbcache/v1/put/'.$key.'/sync';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('put')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
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

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Not found from database");
            return;
        }

        if ($value != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": database data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithJsonValueAndExpire()
    {
        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = 'id4';
        $value = ["f3" => "v333", "f4" => 123];
        $expected_array = ["f1"=>"v41","f2"=>"v42","f3"=>"v333","f4"=>123];
        $table = null;

        $api = '/rdbcache/v1/put/'.$key.'/'.$expire;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('put')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
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
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Not found from database");
            return;
        }

        if ($value != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": database data not match");
            return;
        }

        $this->DatabaseOK();

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

    public function actionSimpleWithJsonValueAndExpireAndSync()
    {
        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = 'id4';
        $value = ["f3" => "v333", "f4" => 123];
        $expected_array = ["f1"=>"v41","f2"=>"v42","f3"=>"v333","f4"=>123];
        $table = null;

        $api = '/rdbcache/v1/put/'.$key.'/'.$expire.'/sync';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('put')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
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

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Not found from database");
            return;
        }

        if ($value != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": database data not match");
            return;
        }

        $this->DatabaseOK();

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

    public function actionWithTable() {

        $this->setup(__FUNCTION__);

        $key = 'my-hash-key-put1';
        $expected_array = ["id"=>1,"name"=>"name11","age"=>10];
        $table = 'tb1';

        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/?id=1';
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $value = ["age"=>29];
        $expected_array = ["id"=>1,"name"=>"name11","age"=>29];

        $api = '/rdbcache/v1/put/'.$key.'/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

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

        // check database
        //
        $record = $this->dbFind(1, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndSync() {

        $this->setup(__FUNCTION__);

        $key = 'my-hash-key-put1';
        $expected_array = ["id"=>1,"name"=>"name11","age"=>10];
        $table = 'tb1';

        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/sync/?id=1';
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $value = ["age"=>29];
        $expected_array = ["id"=>1,"name"=>"name11","age"=>29];

        $api = '/rdbcache/v1/put/'.$key.'/'.$table.'/sync';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/sync';
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

        // check database
        //
        $record = $this->dbFind(1, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndExpire() {

        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = 'my-hash-key-put2';
        $expected_array = ["id"=>1,"name"=>"name11","age"=>10];
        $table = 'tb1';

        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/'.$expire.'/?id=1';
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $value = ["age"=>29];
        $expected_array = ["id"=>1,"name"=>"name11","age"=>29];

        $api = '/rdbcache/v1/put/'.$key.'/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

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

        // check database
        //
        $record = $this->dbFind(1, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        echo "\nsleep $expire seconds\n"; sleep($expire);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, null);
        
        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
            return;
        }

        $this-> RedisOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndExpireAndSync() {

        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = 'my-hash-key-put2';
        $expected_array = ["id"=>1,"name"=>"name11","age"=>10];
        $table = 'tb1';

        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/'.$expire.'/sync/?id=1';
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $value = ["age"=>29];
        $expected_array = ["id"=>1,"name"=>"name11","age"=>29];

        $api = '/rdbcache/v1/put/'.$key.'/'.$table.'/sync';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
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

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/sync';
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

        // check database
        //
        $record = $this->dbFind(1, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        echo "\nsleep $expire seconds\n"; sleep($expire);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, null);
        
        if ($data != null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
            return;
        }

        $this-> RedisOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}
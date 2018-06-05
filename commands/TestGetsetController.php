<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestGetsetController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionSimpleWithSync();

        $this->actionSimpleWithExpire();

        $this->actionSimpleWithExpireAndSync();

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
        $table = null;
        
        $expected_value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($expected_value == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $value = 'value2';
        $api = '/rdbcache/v1/getset/'.$key.'/'.$value;
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
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_value = $value;

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

        $data = $response->data['data'];
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);
        
        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithSync()
    {
        $this->setup(__FUNCTION__);

        $key = 'id1';
        $table = null;
        
        $expected_value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($expected_value == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $value = 'value2';
        $api = '/rdbcache/v1/getset/'.$key.'/'.$value.'/sync';
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
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_value = $value;

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

        $data = $response->data['data'];
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);
        
        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
    
    public function actionSimpleWithExpire()
    {
        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = 'id2';
        $table = null;

        $expected_value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($expected_value == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $value = 'value2';
        $api = '/rdbcache/v1/getset/'.$key.'/'.$value.'/'.$expire;
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
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_value = $value;

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

        $data = $response->data['data'];
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);
        
        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
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

    public function actionSimpleWithExpireAndSync()
    {
        $this->setup(__FUNCTION__);

        $expire = 3;
        $key = 'id2';
        $table = null;

        $expected_value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($expected_value == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $value = 'value2';
        $api = '/rdbcache/v1/getset/'.$key.'/'.$value.'/'.$expire.'/sync';
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
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_value = $value;

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

        $data = $response->data['data'];
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $value = $this->dbFind(['id' => $key, 'type' => 'data']);
        
        if ($value == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($value != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
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

        $table = 'tb1';

        // get expected array from database
        //
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $expected_array = $record;

        $this->DatabaseOK();
        
        $key = 'my-hash-key-getset';
        $value = ["id"=>1,"name"=>"mike","age"=>29];
        //$value = ["name"=>"mike","age"=>29];

        $api = '/rdbcache/v1/getset/'.$key.'/'.$table.'?id=1';
        $response = $this->createRequest(true)
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_array = $value;

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
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }
        
        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in record");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndSync() {
        
        $this->setup(__FUNCTION__);

        $table = 'tb1';

        // get expected array from database
        //
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $expected_array = $record;

        $this->DatabaseOK();
        
        $key = 'my-hash-key-getset';
        $value = ["id"=>1,"name"=>"mike","age"=>29];
        //$value = ["name"=>"mike","age"=>29];

        $api = '/rdbcache/v1/getset/'.$key.'/'.$table.'/sync?id=1';
        $response = $this->createRequest(true)
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_array = $value;

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
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }
        
        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in record");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndExpire() {
        
        $this->setup(__FUNCTION__);

        $table = 'tb1';

        // get expected array from database
        //
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $expected_array = $record;

        $this->DatabaseOK();

        $expire = 3;
        $key = 'my-hash-key-getset2';
        $value = ["id"=>1,"name"=>"mike","age"=>29];
        //$value = ["name"=>"mike","age"=>29];

        $api = '/rdbcache/v1/getset/'.$key.'/'.$table.'/'.$expire.'?id=1';
        $response = $this->createRequest(true)
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_array = $value;

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
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }
        
        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in record");
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

    public function actionWithTableAndExpireAndSync() {
        
        $this->setup(__FUNCTION__);

        $table = 'tb1';

        // get expected array from database
        //
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $expected_array = $record;

        $this->DatabaseOK();

        $expire = 3;
        $key = 'my-hash-key-getset2';
        $value = ["id"=>1,"name"=>"mike","age"=>29];
        //$value = ["name"=>"mike","age"=>29];

        $api = '/rdbcache/v1/getset/'.$key.'/'.$table.'/'.$expire.'/sync?id=1';
        $response = $this->createRequest(true)
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

        $data = $response->data['data'];
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $expected_array = $value;

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
        $record = $this->dbFind(1, $table);

        if ($record == null) {
            $this->NotTestable("data not found in database");
            return;
        }
        
        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data in record");
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
}
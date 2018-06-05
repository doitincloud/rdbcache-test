<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestGet2Controller extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionSimpleWithExpire();

        $this->actionWithTable();

        $this->actionWithTableAndExpire();

        $this->actionWithTableAndGeneratedIdAndExpire();

        $this->actionWithTableAndPlusExpire();

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

        // get by key only, many times
        //
        for ($i = 0; $i < 100; $i++) {
            
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
        }

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithExpire() {

        $this->setup(__FUNCTION__);

        $expire = 3;
        
        // get value from database
        //
        $key = 'id3';
        $table = null;

        $expected_value = $this->dbFind(['id' => $key, 'type' => 'data']);

        if ($expected_value == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/rdbcache/v1/get/'.$key.'/'.$expire;
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

        // wait for expiration
        echo "\nsleep $expire seconds\n"; sleep($expire);

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

        $key = 'my-hash-key-table';
        $api = '/rdbcache/v1/get/'.$key.'/tb1?id=1';
        
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
        }

        $data = $response->data['data'];

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
        }

        $this->HttpOK($response);

        // get by key only, many times
        //
        for ($i = 0; $i < 100; $i++) {
            
            $api = '/rdbcache/v1/get/'.$key.'/tb1';
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
        }

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndExpire() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';
        $expire = 3;

        // get expected from database
        //
        $expected_array = $this->dbFind(1, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $key = 'my-hash-key-table-expire';
        $api = '/rdbcache/v1/get/'.$key.'/'.$expire.'/'.$table.'?id=1';
        
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
        }

        $data = $response->data['data'];

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
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

        // wait for exipration
        echo "\nsleep $expire seconds\n"; sleep($expire);

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

    public function actionWithTableAndGeneratedIdAndExpire() {

        $this->setup(__FUNCTION__);

        $table = 'tb2';

        // get expected from database
        //
        $expected_array = $this->dbFind(['name' => 'name22'], $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $expire = 3;
        $key = '*';
        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/'.$expire.'?name=name22';
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key == $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $data = $response->data['data'];

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $key = $resp_key;

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ( $data != $expected_array) {
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

        // wait for exipration
        echo "\nsleep $expire seconds\n"; sleep($expire);

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

    public function actionWithTableAndPlusExpire() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';
        // get expected from database
        //
        $expected_array = $this->dbFind(1, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $key = 'my-hash-key-plus-expire';
        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/3?id=1';
        
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
        }

        $data = $response->data['data'];

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
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

        // wait for expiration
        echo "\nsleep 2 seconds\n"; sleep(2);

        // get by key with expire=+3
        //
        $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/+5';
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
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}

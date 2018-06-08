<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestPullController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionSimpleWithSync();

        $this->actionWithTable();

        $this->actionWithTableAndSync();
        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionSimple() {

        $this->setup(__FUNCTION__);

        $keys = ['id1', 'id2', 'id3'];
        $expected_arrays = [];
        $table = null;

        // prepare
        //
        foreach ($keys as $key) {

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

            $expected_arrays[$key] = $response->data['data'];

            $this->HttpOK($response);
        }
        echo "expected: ".json_encode($expected_arrays)."\n";

        //pull
        $api = '/rdbcache/v1/pull/';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $data = $response->data['data'];

        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithSync() {

        $this->setup(__FUNCTION__);

        $keys = ['id1', 'id2', 'id3'];
        $expected_arrays = [];
        $table = null;

        // prepare
        //
        foreach ($keys as $key) {

            $api = '/rdbcache/v1/get/'.$key.'/sync';
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

            $expected_arrays[$key] = $response->data['data'];

            $this->HttpOK($response);
        }
        echo "expected: ".json_encode($expected_arrays)."\n";

        //pull
        $api = '/rdbcache/v1/pull/sync';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $data = $response->data['data'];

        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable() {

        $this->setup(__FUNCTION__);

        $ids = [1, 2, 3];
        $keys = [];
        $expected_arrays = [];
        $table = 'tb1';

        // get expected from database
        //
        foreach ($ids as $id) {

            $expected_array = $this->dbFind($id, $table);

            if ($expected_array == null) {
                $this->NotTestable("data not found in database");
                return;
            }

            $this->DatabaseOK();

            $key = 'my-hash-key-'.$id;
            array_push($keys, $key);
            $expected_arrays[$key] = $expected_array;

            $this->DatabaseOK();
        }

        // prepare
        //
        foreach ($ids as $id) {

            $key = 'my-hash-key-'.$id;
            
            $api = '/rdbcache/v1/get/'.$key.'/'.$table.'?id='.$id;
            $response = $this->createRequest()
                ->setMethod('get')
                ->setApi($api)
                ->send();

            if (!$response->isOk) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
                return;
            }

            $this->HttpOK($response);
        }

        //pull
        $api = '/rdbcache/v1/pull/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $data = $response->data['data'];

        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndSync() {

        $this->setup(__FUNCTION__);

        $ids = [1, 2, 3];
        $keys = [];
        $expected_arrays = [];
        $table = 'tb1';

        // get expected from database
        //
        foreach ($ids as $id) {

            $expected_array = $this->dbFind($id, $table);

            if ($expected_array == null) {
                $this->NotTestable("data not found in database");
                return;
            }

            $this->DatabaseOK();

            $key = 'my-hash-key-'.$id;
            array_push($keys, $key);
            $expected_arrays[$key] = $expected_array;

            $this->DatabaseOK();
        }

        // prepare
        //
        foreach ($ids as $id) {

            $key = 'my-hash-key-'.$id;
            
            $api = '/rdbcache/v1/get/'.$key.'/'.$table.'/sync?id='.$id;
            $response = $this->createRequest()
                ->setMethod('get')
                ->setApi($api)
                ->send();

            if (!$response->isOk) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
                return;
            }

            $this->HttpOK($response);
        }

        //pull
        $api = '/rdbcache/v1/pull/sync/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data));
            return;
        }

        $data = $response->data['data'];

        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}

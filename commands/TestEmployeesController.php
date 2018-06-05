<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestEmployeesController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionDepartments();

        $this->actionDepartmentsWithSync();

        $this->actionEmployees();

        $this->actionEmployeesWithSync();

        $this->actionSalaries();

        $this->actionSalariesWithSync();

        $this->actionTitles();

        $this->actionTitlesWithSync();

        $this->actionDeptEmp();

        $this->actionDeptEmpWithSync();

        $this->actionDeptManager();

        $this->actionDeptManagerWithSync();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionDepartments() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'departments';
        $condition = ['dept_no' => 'd001'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDepartmentsWithSync() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'departments';
        $condition = ['dept_no' => 'd001'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'/sync?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
    
    public function actionEmployees() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'employees';
        $condition = ['emp_no' => 10001];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionEmployeesWithSync() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'employees';
        $condition = ['emp_no' => 10001];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'/sync?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
    
    public function actionSalaries() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'salaries';
        $condition = ['emp_no' => 10001, 'from_date' => '1986-06-26'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSalariesWithSync() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'salaries';
        $condition = ['emp_no' => 10001, 'from_date' => '1986-06-26'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'/sync?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionTitles() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'titles';
        $condition = ['emp_no' => 10001, 'from_date' => '1986-06-26'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionTitlesWithSync() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'titles';
        $condition = ['emp_no' => 10001, 'from_date' => '1986-06-26'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'/sync?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptEmp() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'dept_emp';
        $condition = ['emp_no' => 10001, 'dept_no' => 'd005'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptEmpWithSync() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'dept_emp';
        $condition = ['emp_no' => 10001, 'dept_no' => 'd005'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'/sync?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
    
    public function actionDeptManager() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'dept_manager';
        $condition = ['emp_no' => 110039, 'dept_no' => 'd001'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptManagerWithSync() {

        $this->setup(__FUNCTION__);
        $this->empdb_init();

        $table = 'dept_manager';
        $condition = ['emp_no' => 110039, 'dept_no' => 'd001'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/rdbcache/v1/get/*/'.$table.'/sync?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}

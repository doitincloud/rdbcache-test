<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\components;

use Yii;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\Inflector;
use yii\console\Controller;

use app\models\RdbcacheClientTest;

use app\models\RdbcacheKvPair;
use app\models\RdbcacheMonitor;

class TestController extends Controller
{
    protected $current_route;

    protected $main_test_id;

    protected $http_client;

    protected $trace_ids;

    protected function setup($name) {

        echo "\n*************** ";

        $this->current_route = $this->id . '/' . Inflector::camel2id(substr($name, 6));
        $this->stdout($this->current_route . "\n", Console::BOLD);

        $this->db_init();
        
        $redis_conn = Yii::$app->redis;
        $redis_conn->executeCommand('FLUSHALL');

        $this->main_test_id = null;

        $this->http_client = new TestClient();

        $this->trace_ids = [];

        $this->flushLocalCache();

    }

    protected function db_init() {

        $data_conn = Yii::$app->datadb;
        for ($i = 0; $i < 3; $i++) {
            try {
                $sql = file_get_contents(Yii::getAlias('@app') . '/data/datadb_data.sql');
                $command = $data_conn->createCommand($sql);
                $result = $command->execute();
                break;
            } catch (\yii\db\Exception $e) {
                // Serialization failure: 1213 Deadlock is expected
                echo 'Caught exception during setup data, which is expected, sleep 3 seconds and retry';
                sleep(3);
            }
        }
        $system_conn = Yii::$app->systemdb;
        for ($i = 0; $i < 3; $i++) {
            try {
                $sql = file_get_contents(Yii::getAlias('@app') . '/data/systemdb_data.sql');
                $command = $system_conn->createCommand($sql);
                $result = $command->execute();
                break;
            } catch (\yii\db\Exception $e) {
                // Serialization failure: 1213 Deadlock is expected
                echo 'Caught exception during setup data, which is expected, sleep 3 seconds and retry';
                sleep(3);
            }
        }
    }

    protected function empdb_init($withDept2Data = false, $withEmp2Data = false) {

        $dbase_conn = Yii::$app->datadb;
        $sql = file_get_contents(Yii::getAlias('@app') . '/data/emp_data.sql');
        for ($i = 0; $i < 3; $i++) {
            try {
                $command = $dbase_conn->createCommand($sql);
                $result = $command->execute();
                break;
            } catch (\yii\db\Exception $e) {
                // Serialization failure: 1213 Deadlock is expected
                echo 'Caught exception during setup data, which is expected, sleep 3 seconds and retry';
                sleep(3);
            }
        }
        $sql = file_get_contents(Yii::getAlias('@app') . '/data/emp2_data0.sql');
        for ($i = 0; $i < 3; $i++) {
            try {
                $command = $dbase_conn->createCommand($sql);
                $result = $command->execute();
                if ($withDept2Data && $withEmp2Data) {
                    $sql = file_get_contents(Yii::getAlias('@app') . '/data/emp2_data2.sql');
                    $command = $dbase_conn->createCommand($sql);
                    $result = $command->execute();
                    break;
                } else if ($withDept2Data) {
                    $sql = file_get_contents(Yii::getAlias('@app') . '/data/emp2_data1.sql');
                    $command = $dbase_conn->createCommand($sql);
                    $result = $command->execute();
                    break;
                }
            } catch (\yii\db\Exception $e) {
                // Serialization failure: 1213 Deadlock is expected
                echo 'Caught exception during setup data, which is expected, sleep 3 seconds and retry';
                sleep(3);
            }
        }
    }

    protected function flushLocalCache() {

        $api = '/rdbcache/v1/flush-cache';

        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            if (!empty($response->data['message'])) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            } else {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed! " . json_encode($response->data));
            }
        } else {
            $this->HttpOK($response);
        }
    }

    protected function getTestStartAtId() {

        $query = new Query();
        $query->select('max(id) as max_id')->from('rdbcache_client_test');
        $result = $query->one(Yii::$app->systemdb);
        $max_id = $result['max_id'];
        $max_id++;
        return $max_id;
    }

    protected function getTestStopAtId() {
        
        $query = new Query();
        $query->select('max(id) as max_id')->from('rdbcache_client_test');
        $result = $query->one(Yii::$app->systemdb);
        $max_id = $result['max_id'];
        return $max_id;
    }

    protected function createRequest($isMainTest = false) {
        $this->http_client->startNewClientTest($this->current_route);
        if ($isMainTest) {
            $this->setItAsMainTest();
        }
        return $this->http_client->createRequest();
    }

    protected function getTestId() {
        return $this->http_client->getTestId();
    }

    protected function setItAsMainTest() {
        $this->main_test_id = $this->getTestId();
    }

    protected function redisCmd($cmd) {
        echo "\nredis: $cmd\n";
        $conn = Yii::$app->redis;
        $result = $conn->executeCommand($cmd);
        if ($result == null) {
            echo "result: null\n";
            return null;
        } else if (is_string($result)) {
            echo "result(1): $result\n";
            $data = json_decode($result, true);
            if ($data == null) {
                return $result;
            }
            return $data;
        } else if (strpos($cmd, 'HGETALL ') === FALSE) {
            echo "result(2): ".json_encode($result)."\n";
            return $result;
        } else {
            echo "result(3): ".json_encode($result)."\n";
            $assocResult = [];
            for ($i = 0; $i < count($result); $i += 2) {
                $key = $result[$i];
                $value = str_replace("\"", '', $result[$i+1]);
                $assocResult[$key] = $value;
            }
            if (array_key_exists('_DEFAULT_', $assocResult)) {
                return $assocResult['_DEFAULT_'];
            }
            return $assocResult;
        }
    }

    protected function waitForRedisHashUpdate($key, $table, $expected, $field = null, $wait = 40) {
        $data = null;
        $type = "data";
        if ($table != null) {
            $type = $table;
        }
        for ($i = 0; $i < $wait; $i++) {
            $data = $this->redisCmd('HGETALL rdchdata::'.$type.':'.$key);
            if ($expected == 'data' && $data != null) {
                if ($field != null) {
                    if (!empty($data[$field])) {
                        return $data;
                    }
                } else {
                    return $data;
                }
            }
            if ($data == $expected) {
                return $data;
            }
            echo  "wait 0.25 second more\n";
            usleep(250000);
        }
        return $data;
    }

    protected function waitForRedisUpdate($cmd, $expected, $wait = 40) {
        $data = null;
        for ($i = 0; $i < $wait; $i++) {
            $data = $this->redisCmd($cmd);
            if ($expected == 'data' && $data != null) {
                return $data;
            }
            if ($data == $expected) {
                return $data;
            }
            echo  "wait 0.25 second more\n";
            usleep(250000);
        }
        return $data;
    }

    protected function dbFind($condition, $table = null) {
        if (is_array($condition)) {
            echo "\ndbFind: " . json_encode($condition) . ' from '. ($table == null ? 'default' : $table) . "\n";
        } else {
            echo "\ndbFind: " . $condition . ' from '. ($table == null ? 'default' : $table) . "\n";
        }
        if ($table == null) {
            $pair = RdbcacheKvPair::findOne($condition);
            $data = null;
            if ($pair != null) {
                $data = json_decode($pair->value, true);
                if ($data == null) {
                    $data = $pair->value;
                }
                echo 'result: ' . $pair->value . "\n";
            } else {
                echo 'result: null' . "\n";
            }
            return $data;
        }
        
        $model = Inflector::id2camel($table, '_');
        $class = "\\app\\models\\".$model;
        if (is_array($condition)) {
            $record = $class::find()->where($condition)->one();
        } else {
            $record = $class::findOne($condition);
        }
        if ($record == null) {
            echo 'result: null' . "\n";
            return null;
        } else {
            echo 'result: ' . json_encode($record->attributes) . "\n";
        }
        return $record->attributes;
    }

    protected function dbFindAll($table, $limit = 256, $condition = null) {

        if (is_array($condition)) {
            echo "\ndbFindAll: " . json_encode($condition) . ' from '. ($table == null ? 'default' : $table) . "\n";
        } else {
            echo "\ndbFindAll: " . $condition . ' from '. ($table == null ? 'default' : $table) . "\n";
        }

        if ($table == null) {
            if ($condition == null) {
                $records = RdbcacheKvPair::find()->where(['type' => 'data'])->limit($limit)->asArray()->all();
            } else {
                $records = RdbcacheKvPair::find()->where(['type' => 'data'])->andWhere($condition)->limit($limit)->asArray()->all();
            }
            if (empty($records)) {
                echo 'result: empty' . "\n";
                return null;
            }
            for ($i = 0; $i < count($records); $i++) {
                $value = $records[$i]['value'];
                $array = json_decode($value, true);
                if ($array != null) {
                    $records[$i]['value'] = $array;
                }
            }
            echo 'result: ' . json_encode($records) . "\n";
            return $records;
        }

        $model = Inflector::id2camel($table, '_');
        $class = "\\app\\models\\".$model;
        if (empty($condition)) {
            $records = $class::find()->limit($limit)->asArray()->all();
        } else {
            $records = $class::find()->where($condition)->limit($limit)->asArray()->all();
        }
        if (empty($records)) {
            echo 'result: empty' . "\n";
            return null;
        } else {
            echo 'result: ' . json_encode($records) . "\n";
        }
        return $records;
    }

    protected function dbWaitFind($condition, $table = null, $forData = true, $wait = 30) {
        if (is_array($condition)) {
            echo "\ndbFind: " . json_encode($condition) . ' from '. ($table == null ? 'default' : $table) . "\n";
        } else {
            echo "\ndbFind: " . $condition . ' from '. ($table == null ? 'default' : $table) . "\n";
        }

        for ($i = 0; $i < $wait; $i++) {
            if ($table == null) {
                $pair = RdbcacheKvPair::findOne($condition);
                $data = null;
                if ($pair != null) {
                    $data = json_decode($pair->value, true);
                    if ($data == null) {
                        $data = $pair->value;
                    }
                    echo 'result: ' . $pair->value . "\n";
                } else {
                    echo 'result: null' . "\n";
                }
                if ($forData) {
                    if ($data == null) {
                        echo  "wait 1 second more\n";
                        sleep(1);
                        continue;
                    }
                } else {
                    if ($data != null) {
                        echo  "wait 1 second more\n";
                        sleep(1);
                        continue;
                    }
                }
                return $data;
            }
    
            $model = Inflector::id2camel($table, '_');
            $class = "\\app\\models\\".$model;
            if (is_array($condition)) {
                $record = $class::find()->where($condition)->one();
            } else {
                $record = $class::findOne($condition);
            }
            if ($record == null) {
                echo 'result: null' . "\n";
                return null;
            } else {
                echo 'result: ' . json_encode($record->attributes) . "\n";
            }
            if ($forData) {
                if ($record == null) {
                    echo  "wait 1 second more\n";
                    sleep(1);
                    continue;
                }
            } else {
                if ($record != null) {
                    echo  "wait 1 second more\n";
                    sleep(1);
                    continue;
                }
            }
            return $record->attributes;
        }
    }

    protected function dbWaitFindAll($table, $count_expected, $condition = null, $wait = 30) {
        $model = Inflector::id2camel($table, '_');
        $class = "\\app\\models\\".$model;

        for ($i = 0; $i < $wait; $i++) {
            if (empty($condtion)) {
                $count = $class::find()->count();
            } else {
                $count = $class::find()->where($condition)->count();
            }
            if ($count >= $count_expected) {
                break;
            }
            echo  "wait 1 second more\n";
            sleep(1);
        }
        if (empty($condtion)) {
            $records = $class::find()->limit($count_expected)->asArray()->all();
        } else {
            $records = $class::find()->where($condition)->limit($count_expected)->asArray()->all();
        }
        if (empty($records)) {
            echo 'result: empty' . "\n";
            return null;
        } else {
            echo 'result: ' . json_encode($records) . "\n";
        }
        return $records;
    }

    protected function failed($message) {
        $this->stdout($message . "\n", Console::BOLD, Console::FG_RED);
        $this->http_client->addClientMessage($message);
    }

    protected function passed($message = '') {
        $this->http_client->updatePassed(true);
        $this->http_client->updateVerifyPassed(true);
        $this->stdout("\nPassed! $message\n", Console::BOLD);
        if (!empty($message)) {
            $this->http_client->addClientMessage($message);
        }
    }

    protected function RedisOK($message = '') {
        echo "\nRedis OK! $message\n";
        if (!empty($message)) {
            $this->http_client->addClientMessage($message);
        }
    }

    protected function DatabaseOK($message = '') {
        echo "\nDatabase OK! $message\n";
        if (!empty($message)) {
            $this->http_client->addClientMessage($message);
        }
    }

    protected function HttpOK($response, $push_trace_id = true) {

        if ($push_trace_id) {
            array_push($this->trace_ids, $response->data['trace_id']);
        }
        $this->http_client->updatePassed(true);
        if ($this->main_test_id == null || $this->main_test_id != $this->getTestId()) {
            $this->http_client->updateVerifyPassed(true);
        }
        if (!empty($response->data['duration'])) {
            $duration = $response->data['duration'];
            $this->stdout("\nOK! Durations in seconds => process: $duration client: ".
                //$this->http_client->duration,
                number_format($this->http_client->duration/1000000000.0, 6)."\n",
                Console::BOLD);
        } else {
            $this->stdout("\nNot OK! response data format incorrect\n",
                Console::BOLD, Console::FG_RED);
        }
    }

    protected function VerifyOK($fileline, $message = '') {

        // wait for all expiration events to finish
        //
        for ($i = 0; $i < 61; $i++) {
            $data = $this->redisCmd('keys rdbevent::*');
            if ($data == null) {
                break;
            }
            echo  "wait 3 seconds more\n";
            sleep(3);
        }
        
        // check for trace by trace_ids
        //
        $api = '/rdbcache/v1/trace/';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($this->trace_ids)
            ->send();

        if (!$response->isOk) {
            $this->failed($fileline.": Failed! " . json_encode($response->data));
            return;
        }

        $this->HttpOK($response, false);

        $data = $response->data['data'];
        if (empty($data) || count($data) != count($this->trace_ids)) {
            $this->failed($fileline.": Failed! incorrect data");
            return;
        }

        foreach($this->trace_ids as $trace_id) {
            if (!array_key_exists($trace_id, $data) || $data[$trace_id] != []) {
                $this->failed($fileline.": Failed! error message found by trace id");
                return;
            }
        }

        $this->stdout("\nVerify OK! $message\n", Console::BOLD, Console::FG_BLUE);
        if ($this->main_test_id == $this->getTestId()) {
            $this->http_client->updateVerifyPassed(true);
        } else {
            $clientTest = RdbcacheClientTest::findOne($this->main_test_id);
            if ($clientTest != null) {
                $clientTest->verify_passed = true;
                $clientTest->save(false);
            }
        }
    }
    
    protected function NotTestable($message) {
        echo "\nNot testable: $message\n";
        $this->http_client->addClientMessage("Not testable: $message");
    }

    private function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    protected function summary($start_id, $stop_id, $printTitle = true) {
        if ($printTitle) {
            $this->stdout("\n*************** SUMMARY ***************\n", Console::BOLD);
        }
        $total = $stop_id - $start_id + 1;
        echo "\nTotal: ".($total) ."\n";
        $query = new Query;
        $query->select('count(*) as failed')
            ->from('rdbcache_client_test')
            ->where("id >= ".$start_id." AND id <= ".$stop_id." AND NOT passed");
        $result = $query->one(Yii::$app->systemdb);
        $failed = intval($result['failed']);
        if ($failed > 0) {
            $this->stdout("Failed: " . $failed . "\n", Console::BOLD, Console::FG_RED);
        } else {
            echo "All passed!\n";
        }
        $query->select('count(*) as verify_failed')
            ->from('rdbcache_client_test')
            ->where("id >= ".$start_id." AND id <= ".$stop_id." AND NOT verify_passed");
        $result = $query->one(Yii::$app->systemdb);
        $verify_failed = intval($result['verify_failed']);
        if ($verify_failed > 0) {
            $this->stdout("Verify Failed: " . $verify_failed . "\n", Console::BOLD, Console::FG_RED);
        } else {
            echo "All verify passed!\n";
        }
        if ($failed > 0) {
            echo "\nFailed test(s):\n";
            $query->select("*")->from('rdbcache_client_test')
                ->where("id >= ".$start_id." AND id <= ".$stop_id." AND NOT passed");
            $result = $query->all(Yii::$app->systemdb);
            for ($i = 0; $i < $failed; $i++) {
                $record = $result[$i];
                $this->stdout("\n".$record['id'].") route: ".$record['route']."\nurl: ".$record['url'] . "\n", Console::BOLD, Console::FG_RED);
            }
        }
        if ($verify_failed > 0) {
            echo "\nVerify failed test(s):\n";
            $query->select("*")->from('rdbcache_client_test')
                ->where("id >= ".$start_id." AND id <= ".$stop_id." AND NOT verify_passed");
            $result = $query->all(Yii::$app->systemdb);
            for ($i = 0; $i < $verify_failed; $i++) {
                $record = $result[$i];
                $this->stdout("\n".$record['id'].") route: ".$record['route']."\nurl: ".$record['url'] . "\n", Console::BOLD, Console::FG_RED);
            }
        }
        $monitor_start_id = null;
        $monitor_stop_id = null;
        for ($i = $start_id; $i <= $stop_id; $i++) {
            $client_test = RdbcacheClientTest::findOne($i);
            if ($client_test == null) continue;
            $trace_id = $client_test->trace_id;
            $monitor = RdbcacheMonitor::find()->where(['trace_id' => $trace_id])->one();
            if ($monitor != null) {
                if ($monitor_start_id == null) $monitor_start_id = $monitor->id;
                $monitor_stop_id = $monitor->id;
                $monitor->client_duration = $client_test->duration;
                $monitor->save(false);

                $client_test->process_duration = $monitor->main_duration;
                $client_test->save(false);
            }
        }
    }
}

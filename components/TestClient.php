<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\components;

use Yii;
use yii\httpclient\Client;
use yii\helpers\ArrayHelper;

use app\models\RdbcacheClientTest;

class TestClient extends Client {

    private $_start_time;

    private $_duration;

    private $client_test;

    public function init() {

        $this->requestConfig = ['class' => 'app\components\TestRequest'];
        
        $this->on(self::EVENT_BEFORE_SEND, function ($event) {
            $test_id = '';
            if ($this->client_test != null) {
                $test_id = $this->client_test->id . ')'."\n";
            }
            $request = $event->request;
            if (!empty($request->headers['authorization'])) {
                echo "\naccess_token: " . substr($request->headers['authorization'], 7);
            }
            echo "\n" . $test_id . strtoupper($request->method) . ' ' . $request->url . "\n";
            if ($request->data != null) {
                $data = $request->data;
                if (is_string($data)) {
                    echo 'data: '.$data . "\n";
                } else {
                    echo 'data: '.json_encode($data) . "\n";
                }
            }
            $this->start_nano_timer();
        });

        $this->on(self::EVENT_AFTER_SEND, function ($event) {
            $this->stop_nano_timer();
            $url = $event->request->url;
            $response = $event->response;
            if ($response->isOk) {
                $data = $response->data;
                if (is_string($data)) {
                    echo 'respond: '.$data . "\n";
                } else {
                    echo 'respond: '.json_encode($data) . "\n";
                }
            }
            $this->saveResult($event->request, $response);
        });
    }

    public function getDuration() {
        return $this->_duration;
    }

    public function startNewClientTest($route) {
        $this->client_test = new RdbcacheClientTest();
        $this->client_test->route = $route;
        $this->client_test->save(false);
    }

    public function saveResult($request, $response) {

        if ($this->client_test == null) {
            $this->client_test = new RdbcacheClientTest();
        }
        $this->client_test->status = $response->statusCode;
        $this->client_test->duration = $this->_duration;
        if ($response->isOk && $response->data != null) {
            if (!empty($response->data['duration'])) {
                $this->client_test->process_duration = $response->data['duration'] * 1000000000;
            }
            if (!empty($response->data['trace_id'])) {
                $this->client_test->trace_id = $response->data['trace_id'];
            }
        }
        $this->client_test->url = $request->url;
        $this->client_test->passed = false;
        $this->client_test->verify_passed = false;
        $data = [];
        $data['request'] = [
            'method'=>$request->method, 
            'headers'=>$request->headers->toArray(),
        ];
        $content = json_encode($request->data);
        if (strlen($content) > 2048) {
            $data['request']['content'] = '...';
        } else {
            $data['request']['content'] = $request->data;
        }
        $data['response'] = [
            'headers'=>$response->headers->toArray(), 
        ];
        $content = json_encode($response->data);
        if (strlen($content) > 2048) {
            $data['response']['content'] = '...';
        } else {
            $data['response']['content'] = $response->data;
        }
        $this->client_test->data = json_encode($data);
        $this->client_test->save(false);
    }

    public function getTestId() {
        if ($this->client_test == null) {
            return null;
        }
        return $this->client_test->id;
    }

    public function updatePassed($passed) {
        if ($this->client_test == null) {
            return;
        }
        $this->client_test->passed = $passed;
        $this->client_test->save(false);
    }

    public function updateVerifyPassed($passed) {
        if ($this->client_test == null) {
            return;
        }
        $this->client_test->verify_passed = $passed;
        $this->client_test->save(false);
    }

    public function addClientMessage($message) {
        if ($this->client_test == null) {
            return;
        }
        if ($this->client_test->data == null) {
            $data = [];
        } else {
            $data = json_decode($this->client_test->data, true);
        }
        if (empty($data['messages'])) {
            $data['messages'] = array($message);
        } else {
            array_push($data['messages'], $message);
        }
        $this->client_test->data = json_encode($data);
        $this->client_test->save(false);
    }

    private function nanotime() {
        list($usec, $sec) = explode(" ", microtime());
        $sec = round($sec / 100) * 100;
        return round($usec * 1000000000 +  $sec * 1000000000);
    }

    private function start_nano_timer()
    {
        $this->_start_time = $this->nanotime();
    }

    private function stop_nano_timer() {
        $duration = $this->nanotime() - $this->_start_time;
        if ($duration < 0) $duration += 1000000000;
        $this->_duration = $duration;
    }
}

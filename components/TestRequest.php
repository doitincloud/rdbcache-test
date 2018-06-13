<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\components;

use Yii;
use yii\httpclient\Client;
use yii\httpclient\Request;

class TestRequest extends Request {

    protected $server;

    protected $server_port;
    
    protected static $access_token;

    public function init() {

        $this->server = Yii::$app->params['rdbcache_server'];
        $this->server_port = Yii::$app->params['rdbcache_port'];
        if (empty(self::$access_token) && !empty(Yii::$app->params['oauth2server_url'])) {
            $client = new Client();

            $request = $client->createRequest()
                ->setMethod('post')
                ->setUrl(Yii::$app->params['oauth2server_url'] . "/oauth/v1/token")
                ->addHeaders(['Authorization' => 'Basic '.
                    base64_encode(
                        Yii::$app->params['oauth2server_client_id'].':'.
                        Yii::$app->params['oauth2server_client_secret'])]
                    )
                ->setData([
                    'grant_type' => 'password',
                    'username' => Yii::$app->params['oauth2server_user_name'],
                    'password' => Yii::$app->params['oauth2server_user_password'],
                    'scope' => 'read write delete'
                ]);
            $response = $request->send();
            
            if (!$response->isOk) {
                $message = basename(__FILE__)."@".__LINE__.": Failed!  " . json_encode($response->data);
                $this->stdout($message . "\n", Console::BOLD, Console::FG_RED);
                return;
            }
            self::$access_token = $response->data['access_token'];
        }
    }

    public function setApi($api) {
        if (!empty(self::$access_token)) {
            $this->addHeaders(['Authorization' => 'Bearer ' . self::$access_token]);
        }
        $this->setUrl('http://'.$this->server.':'.$this->server_port . $api);
        return $this;
    }
}
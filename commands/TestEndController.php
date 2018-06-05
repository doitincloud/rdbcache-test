<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use yii\helpers\Console;

use app\components\TestController;

class TestEndController extends TestController
{
    public function actionIndex($start_id)
    {
        $this->stdout("\n=============== OVERALL ===============\n", Console::BOLD);
        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id, false);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rdbcache_client_test".
 *
 * @property int $id
 * @property string $trace_id
 * @property string $status
 * @property int $passed
 * @property int $verify_passed
 * @property int $duration
 * @property int $process_duration
 * @property string $route
 * @property string $url
 * @property string $data
 */
class RdbcacheClientTest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rdbcache_client_test';
    }

    public static function getDb()
    {
        return Yii::$app->systemdb;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['passed', 'verify_passed', 'duration', 'process_duration'], 'integer'],
            [['data'], 'string'],
            [['trace_id'], 'string', 'max' => 64],
            [['status'], 'string', 'max' => 32],
            [['route', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trace_id' => 'Reference ID',
            'status' => 'Status',
            'passed' => 'Passed',
            'verify_passed' => 'Verify Passed',
            'duration' => 'Duration',
            'process_duration' => 'Process Duration',
            'route' => 'Route',
            'url' => 'Url',
            'data' => 'Data',
        ];
    }

    /**
     * @inheritdoc
     * @return RdbcacheClientTestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RdbcacheClientTestQuery(get_called_class());
    }
}

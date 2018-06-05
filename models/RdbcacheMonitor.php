<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rdbcache_monitor".
 *
 * @property int $id
 * @property string $name
 * @property int $thread_id
 * @property int $duration
 * @property int $main_duration
 * @property int $client_duration
 * @property int $started_at
 * @property int $ended_at
 * @property string $trace_id
 * @property string $built_info
 *
 * @property RdbcacheStopwatch[] $rdbcacheStopwatches
 */
class RdbcacheMonitor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rdbcache_monitor';
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
            [['name'], 'required'],
            [['thread_id', 'duration', 'main_duration', 'client_duration', 'started_at', 'ended_at'], 'integer'],
            [['name', 'built_info'], 'string', 'max' => 255],
            [['trace_id'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'thread_id' => 'Thread ID',
            'duration' => 'Duration',
            'main_duration' => 'Main Duration',
            'client_duration' => 'Client Duration',
            'started_at' => 'Started At',
            'ended_at' => 'Ended At',
            'trace_id' => 'Trace ID',
            'built_info' => 'Built Info',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRdbcacheStopwatches()
    {
        return $this->hasMany(RdbcacheStopwatch::className(), ['monitor_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return RdbcacheMonitorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RdbcacheMonitorQuery(get_called_class());
    }
}

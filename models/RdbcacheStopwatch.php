<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rdbcache_stopwatch".
 *
 * @property int $id
 * @property int $monitor_id
 * @property string $type
 * @property string $action
 * @property int $thread_id
 * @property int $duration
 * @property int $started_at
 * @property int $ended_at
 *
 * @property RdbcacheMonitor $monitor
 */
class RdbcacheStopwatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rdbcache_stopwatch';
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
            [['monitor_id', 'type'], 'required'],
            [['monitor_id', 'thread_id', 'duration', 'started_at', 'ended_at'], 'integer'],
            [['type'], 'string', 'max' => 16],
            [['action'], 'string', 'max' => 255],
            [['monitor_id'], 'exist', 'skipOnError' => true, 'targetClass' => RdbcacheMonitor::className(), 'targetAttribute' => ['monitor_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'monitor_id' => 'Monitor ID',
            'type' => 'Type',
            'action' => 'Action',
            'thread_id' => 'Thread ID',
            'duration' => 'Duration',
            'started_at' => 'Started At',
            'ended_at' => 'Ended At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitor()
    {
        return $this->hasOne(RdbcacheMonitor::className(), ['id' => 'monitor_id']);
    }

    /**
     * @inheritdoc
     * @return RdbcacheStopwatchQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RdbcacheStopwatchQuery(get_called_class());
    }
}

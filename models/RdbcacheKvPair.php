<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rdbcache_kv_pair".
 *
 * @property string $id
 * @property string $type
 * @property string $value
 */
class RdbcacheKvPair extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rdbcache_kv_pair';
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
            [['id', 'type'], 'required'],
            [['value'], 'string'],
            [['id'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 16],
            [['id', 'type'], 'unique', 'targetAttribute' => ['id', 'type']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'value' => 'Value',
        ];
    }

    /**
     * @inheritdoc
     * @return RdbcacheKvPairQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RdbcacheKvPairQuery(get_called_class());
    }
}

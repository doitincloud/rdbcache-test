<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb1".
 *
 * @property int $id
 * @property string $name
 * @property int $age
 */
class Tb1 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb1';
    }

    public static function getDb()
    {
        return Yii::$app->datadb;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['age'], 'integer'],
            [['name'], 'string', 'max' => 16],
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
            'age' => 'Age',
        ];
    }

    /**
     * @inheritdoc
     * @return Tb1Query the active query used by this AR class.
     */
    public static function find()
    {
        return new Tb1Query(get_called_class());
    }
}

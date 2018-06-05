<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb2".
 *
 * @property string $id
 * @property string $name
 * @property string $dob
 */
class Tb2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb2';
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
            [['id'], 'required'],
            [['dob'], 'safe'],
            [['id'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['id'], 'unique'],
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
            'dob' => 'Dob',
        ];
    }

    /**
     * @inheritdoc
     * @return Tb2Query the active query used by this AR class.
     */
    public static function find()
    {
        return new Tb2Query(get_called_class());
    }
}

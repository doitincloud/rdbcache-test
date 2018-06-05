<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb3".
 *
 * @property string $name
 * @property string $dv
 * @property double $fv
 * @property int $iv
 * @property int $bv
 * @property string $t1v
 * @property string $t2v
 * @property string $t3v
 * @property string $t4v
 * @property string $t5v
 */
class Tb3 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb3';
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
            [['dv', 'fv'], 'number'],
            [['iv', 'bv'], 'integer'],
            [['t1v', 't2v', 't3v', 't4v', 't5v'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'dv' => 'Dv',
            'fv' => 'Fv',
            'iv' => 'Iv',
            'bv' => 'Bv',
            't1v' => 'T1v',
            't2v' => 'T2v',
            't3v' => 'T3v',
            't4v' => 'T4v',
            't5v' => 'T5v',
        ];
    }

    /**
     * @inheritdoc
     * @return Tb3Query the active query used by this AR class.
     */
    public static function find()
    {
        return new Tb3Query(get_called_class());
    }
}

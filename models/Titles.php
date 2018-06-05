<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "titles".
 *
 * @property int $emp_no
 * @property string $title
 * @property string $from_date
 * @property string $to_date
 *
 * @property Employees $empNo
 */
class Titles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'titles';
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
            [['emp_no', 'title', 'from_date'], 'required'],
            [['emp_no'], 'integer'],
            [['from_date', 'to_date'], 'safe'],
            [['title'], 'string', 'max' => 50],
            [['emp_no', 'title', 'from_date'], 'unique', 'targetAttribute' => ['emp_no', 'title', 'from_date']],
            [['emp_no'], 'exist', 'skipOnError' => true, 'targetClass' => Employees::className(), 'targetAttribute' => ['emp_no' => 'emp_no']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'emp_no' => 'Emp No',
            'title' => 'Title',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpNo()
    {
        return $this->hasOne(Employees::className(), ['emp_no' => 'emp_no']);
    }

    /**
     * @inheritdoc
     * @return TitlesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TitlesQuery(get_called_class());
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dept_manager".
 *
 * @property string $dept_no
 * @property int $emp_no
 * @property string $from_date
 * @property string $to_date
 *
 * @property Employees $empNo
 * @property Departments $deptNo
 */
class DeptManager extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dept_manager';
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
            [['dept_no', 'emp_no', 'from_date', 'to_date'], 'required'],
            [['emp_no'], 'integer'],
            [['from_date', 'to_date'], 'safe'],
            [['dept_no'], 'string', 'max' => 4],
            [['dept_no', 'emp_no'], 'unique', 'targetAttribute' => ['dept_no', 'emp_no']],
            [['emp_no'], 'exist', 'skipOnError' => true, 'targetClass' => Employees::className(), 'targetAttribute' => ['emp_no' => 'emp_no']],
            [['dept_no'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::className(), 'targetAttribute' => ['dept_no' => 'dept_no']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dept_no' => 'Dept No',
            'emp_no' => 'Emp No',
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
     * @return \yii\db\ActiveQuery
     */
    public function getDeptNo()
    {
        return $this->hasOne(Departments::className(), ['dept_no' => 'dept_no']);
    }

    /**
     * @inheritdoc
     * @return DeptManagerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DeptManagerQuery(get_called_class());
    }
}

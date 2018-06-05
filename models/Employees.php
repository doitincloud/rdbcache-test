<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "employees".
 *
 * @property int $emp_no
 * @property string $birth_date
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $hire_date
 *
 * @property DeptEmp[] $deptEmps
 * @property Departments[] $deptNos
 * @property DeptManager[] $deptManagers
 * @property Departments[] $deptNos0
 * @property Salaries[] $salaries
 * @property Titles[] $titles
 */
class Employees extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employees';
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
            [['emp_no', 'birth_date', 'first_name', 'last_name', 'gender', 'hire_date'], 'required'],
            [['emp_no'], 'integer'],
            [['birth_date', 'hire_date'], 'safe'],
            [['gender'], 'string'],
            [['first_name'], 'string', 'max' => 14],
            [['last_name'], 'string', 'max' => 16],
            [['emp_no'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'emp_no' => 'Emp No',
            'birth_date' => 'Birth Date',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'gender' => 'Gender',
            'hire_date' => 'Hire Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeptEmps()
    {
        return $this->hasMany(DeptEmp::className(), ['emp_no' => 'emp_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeptNos()
    {
        return $this->hasMany(Departments::className(), ['dept_no' => 'dept_no'])->viaTable('dept_emp', ['emp_no' => 'emp_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeptManagers()
    {
        return $this->hasMany(DeptManager::className(), ['emp_no' => 'emp_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeptNos0()
    {
        return $this->hasMany(Departments::className(), ['dept_no' => 'dept_no'])->viaTable('dept_manager', ['emp_no' => 'emp_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalaries()
    {
        return $this->hasMany(Salaries::className(), ['emp_no' => 'emp_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitles()
    {
        return $this->hasMany(Titles::className(), ['emp_no' => 'emp_no']);
    }

    /**
     * @inheritdoc
     * @return EmployeesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmployeesQuery(get_called_class());
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "departments".
 *
 * @property string $dept_no
 * @property string $dept_name
 *
 * @property DeptEmp[] $deptEmps
 * @property Employees[] $empNos
 * @property DeptManager[] $deptManagers
 * @property Employees[] $empNos0
 */
class Departments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'departments';
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
            [['dept_no', 'dept_name'], 'required'],
            [['dept_no'], 'string', 'max' => 4],
            [['dept_name'], 'string', 'max' => 40],
            [['dept_name'], 'unique'],
            [['dept_no'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dept_no' => 'Dept No',
            'dept_name' => 'Dept Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeptEmps()
    {
        return $this->hasMany(DeptEmp::className(), ['dept_no' => 'dept_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpNos()
    {
        return $this->hasMany(Employees::className(), ['emp_no' => 'emp_no'])->viaTable('dept_emp', ['dept_no' => 'dept_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeptManagers()
    {
        return $this->hasMany(DeptManager::className(), ['dept_no' => 'dept_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpNos0()
    {
        return $this->hasMany(Employees::className(), ['emp_no' => 'emp_no'])->viaTable('dept_manager', ['dept_no' => 'dept_no']);
    }

    /**
     * @inheritdoc
     * @return DepartmentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentsQuery(get_called_class());
    }
}

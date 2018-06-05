<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "employees2".
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
class Employees2 extends Employees
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employees2';
    }
}

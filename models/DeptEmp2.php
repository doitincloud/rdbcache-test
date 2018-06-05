<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dept_emp2".
 *
 * @property int $emp_no
 * @property string $dept_no
 * @property string $from_date
 * @property string $to_date
 *
 * @property Employees $empNo
 * @property Departments $deptNo
 */
class DeptEmp2 extends DeptEmp
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dept_emp2';
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dept_manager2".
 *
 * @property string $dept_no
 * @property int $emp_no
 * @property string $from_date
 * @property string $to_date
 *
 * @property Employees $empNo
 * @property Departments $deptNo
 */
class DeptManager2 extends DeptManager
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dept_manager2';
    }
}

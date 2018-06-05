<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "departments2".
 *
 * @property string $dept_no
 * @property string $dept_name
 *
 * @property DeptEmp[] $deptEmps
 * @property Employees[] $empNos
 * @property DeptManager[] $deptManagers
 * @property Employees[] $empNos0
 */
class Departments2 extends Departments
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'departments2';
    }
}

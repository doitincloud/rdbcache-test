<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salaries2".
 *
 * @property int $emp_no
 * @property int $salary
 * @property string $from_date
 * @property string $to_date
 *
 * @property Employees $empNo
 */
class Salaries2 extends Salaries
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salaries2';
    }
}

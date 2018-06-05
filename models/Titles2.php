<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "titles2".
 *
 * @property int $emp_no
 * @property string $title
 * @property string $from_date
 * @property string $to_date
 *
 * @property Employees $empNo
 */
class Titles2 extends Titles
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'titles2';
    }
}

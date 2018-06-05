<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[DeptEmp]].
 *
 * @see DeptEmp
 */
class DeptEmpQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DeptEmp[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DeptEmp|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

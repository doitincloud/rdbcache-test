<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[DeptManager]].
 *
 * @see DeptManager
 */
class DeptManagerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DeptManager[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DeptManager|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

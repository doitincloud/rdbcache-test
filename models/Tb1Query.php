<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Tb1]].
 *
 * @see Tb1
 */
class Tb1Query extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Tb1[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Tb1|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Tb2]].
 *
 * @see Tb2
 */
class Tb2Query extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Tb2[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Tb2|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

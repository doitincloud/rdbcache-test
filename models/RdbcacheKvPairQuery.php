<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RdbcacheKvPair]].
 *
 * @see RdbcacheKvPair
 */
class RdbcacheKvPairQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return RdbcacheKvPair[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RdbcacheKvPair|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

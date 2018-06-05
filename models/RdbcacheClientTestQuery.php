<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RdbcacheClientTest]].
 *
 * @see RdbcacheClientTest
 */
class RdbcacheClientTestQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return RdbcacheClientTest[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RdbcacheClientTest|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

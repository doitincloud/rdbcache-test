<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RdbcacheClientTest;

/**
 * RdbcacheClientTestSearch represents the model behind the search form of `app\models\RdbcacheClientTest`.
 */
class RdbcacheClientTestSearch extends RdbcacheClientTest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'passed', 'verify_passed', 'duration', 'process_duration'], 'integer'],
            [['trace_id', 'status', 'route', 'url', 'data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = RdbcacheClientTest::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'passed' => $this->passed,
            'verify_passed' => $this->verify_passed,
            'duration' => $this->duration,
            'process_duration' => $this->process_duration,
        ]);

        $query->andFilterWhere(['like', 'trace_id', $this->trace_id])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}

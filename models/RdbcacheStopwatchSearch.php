<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RdbcacheStopwatch;

/**
 * RdbcacheStopwatchSearch represents the model behind the search form of `app\models\RdbcacheStopwatch`.
 */
class RdbcacheStopwatchSearch extends RdbcacheStopwatch
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'monitor_id', 'thread_id', 'duration', 'started_at', 'ended_at'], 'integer'],
            [['type', 'action'], 'safe'],
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
        $query = RdbcacheStopwatch::find();

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
            'monitor_id' => $this->monitor_id,
            'thread_id' => $this->thread_id,
            'duration' => $this->duration,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'action', $this->action]);

        return $dataProvider;
    }
}

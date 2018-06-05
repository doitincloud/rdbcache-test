<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RdbcacheMonitor;

/**
 * RdbcacheMonitorSearch represents the model behind the search form of `app\models\RdbcacheMonitor`.
 */
class RdbcacheMonitorSearch extends RdbcacheMonitor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'thread_id', 'duration', 'main_duration', 'client_duration', 'started_at', 'ended_at'], 'integer'],
            [['name', 'trace_id', 'built_info'], 'safe'],
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
        $query = RdbcacheMonitor::find();

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
            'thread_id' => $this->thread_id,
            'duration' => $this->duration,
            'main_duration' => $this->main_duration,
            'client_duration' => $this->client_duration,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'trace_id', $this->trace_id])
            ->andFilterWhere(['like', 'built_info', $this->built_info]);

        return $dataProvider;
    }
}

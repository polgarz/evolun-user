<?php

namespace evolun\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * A felhasználók kereséséhez szükséges model
 */
class UserSearch extends User
{
    public $searchString;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['searchString'], 'safe'],
        ];
    }

    /**
     * Keresés
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 50,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->orFilterWhere(['like', 'user.name', $this->searchString])
            ->orFilterWhere(['like', 'user.nickname', $this->searchString])
            ->orFilterWhere(['like', 'user.email', $this->searchString])
            ->orFilterWhere(['like', 'user.skype', $this->searchString])
            ->orFilterWhere(['like', 'user.phone', $this->searchString]);

        return $dataProvider;
    }
}

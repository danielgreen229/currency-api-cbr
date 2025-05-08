<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currency_rates".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property float $rate
 * @property string $date
 * @property string|null $created_at
 */
class CurrencyRate extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_rates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name', 'rate', 'date'], 'required'],
            [['rate'], 'number'],
            [['date', 'created_at'], 'safe'],
            [['code'], 'string', 'max' => 3],
            [['name'], 'string', 'max' => 255],
            [['code', 'date'], 'unique', 'targetAttribute' => ['code', 'date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'rate' => 'Rate',
            'date' => 'Date',
            'created_at' => 'Created At',
        ];
    }

}

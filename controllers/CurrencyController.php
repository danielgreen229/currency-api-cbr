<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use app\models\CurrencyRate;
use yii\base\DynamicModel;

class CurrencyController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Получение курса валюты
     * 
     * @param string $code Трехбуквенный код валюты (ISO 4217)
     * @param string|null $date Дата в формате YYYY-MM-DD
     * @return array
     * @throws BadRequestHttpException|NotFoundHttpException
     */
    public function actionRate($code, $date = null)
    {
        $date = $this->normalizeDate($date);
        $code = $this->validateCurrencyCode($code);
        $rate = $this->findCurrencyRate($code, $date);
        
        return $this->formatResponse($rate);
    }

    /**
     * Нормализация и валидация даты
     */
    protected function normalizeDate($date): string
    {
        // Установка даты по умолчанию
        if ($date === null) {
            return date('Y-m-d', strtotime('+1 day'));
        }

        // Валидация явно переданной даты
        $model = DynamicModel::validateData(['date' => $date], [
            ['date', 'date', 'format' => 'php:Y-m-d'],
            [
                'date',
                function ($attribute, $params, $validator) {
                    if (strtotime($this->$attribute) > time()) {
                        $this->addError($attribute, 'Нельзя запрашивать курс будущих дат');
                    }
                }
            ]
        ]);

        if ($model->hasErrors()) {
            throw new BadRequestHttpException(current($model->getFirstErrors()));
        }

        return $date;
    }

    /**
     * Валидация кода валюты
     */
    protected function validateCurrencyCode($code): string
    {
        $code = strtoupper(trim($code));
        $model = DynamicModel::validateData(['code' => $code], [
            ['code', 'match', 'pattern' => '/^[A-Z]{3}$/']
        ]);

        if ($model->hasErrors()) {
            throw new BadRequestHttpException('Неверный формат кода валюты');
        }

        return $code;
    }

    /**
     * Поиск курса валюты
     */
    protected function findCurrencyRate($code, $date): CurrencyRate
    {
        $rate = CurrencyRate::find()
            ->where(['code' => $code, 'date' => $date])
            ->cache(300) // 5 минут кэширования
            ->one();

        if (!$rate) {
            throw new NotFoundHttpException('Курс на указанную дату не найден - '.$date);
        }

        return $rate;
    }

    /**
     * Форматирование ответа
     */
    protected function formatResponse(CurrencyRate $rate): array
    {
        return [
            'code' => $rate->code,
            'rate' => number_format($rate->rate, 4, '.', ''),
            'date' => $rate->date,
        ];
    }
}
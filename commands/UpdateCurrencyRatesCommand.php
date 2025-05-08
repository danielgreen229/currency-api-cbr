<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\httpclient\Client;
use app\models\CurrencyRate;
use yii\helpers\ArrayHelper;

class UpdateCurrencyRatesCommand extends Controller
{
    public function actionRun()
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('http://www.cbr.ru/scripts/XML_daily.asp')
            ->send();

        if (!$response->isOk) {
            $this->stderr('Ошибка получения CBR' . PHP_EOL);
            return ExitCode::UNAVAILABLE;
        }

        $xml = simplexml_load_string($response->content);
        $date = (string)$xml->attributes()['Date'];
        $date = \DateTime::createFromFormat('d.m.Y', $date)->format('Y-m-d');

        $existingRates = CurrencyRate::find()
            ->where(['date' => $date])
            ->indexBy('code')
            ->all();

        $processedCodes = [];

        foreach ($xml->Valute as $valute) {
            $code = (string)$valute->CharCode;
            $name = (string)$valute->Name;
            $rate = (float)str_replace(',', '.', (string)$valute->Value);
            $nominal = (int)$valute->Nominal;

            $rate = $rate / $nominal;

            $model = $existingRates[$code] ?? new CurrencyRate();

            $model->code = $code;
            $model->name = $name;
            $model->rate = $rate;
            $model->date = $date;

            if (!$model->save()) {
                $this->stderr("Ошибка сохранения {$code}: " . print_r($model->errors, true) . PHP_EOL);
                continue;
            }

            $processedCodes[] = $code;
        }

        CurrencyRate::deleteAll([
            'and',
            ['date' => $date],
            ['not in', 'code', $processedCodes]
        ]);

        $this->stdout("Курс обновлен на {$date}" . PHP_EOL);
        return ExitCode::OK;
    }
}
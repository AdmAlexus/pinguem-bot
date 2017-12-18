<?php

namespace app\controllers;

use app\models\Commands;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class BotController extends Controller
{

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Отрабатывает, когда telegram шлёт сообщение
     * @return array
     */

    public function actionIndex() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $bot = new Telegram(Yii::$app->params['botToken'], Yii::$app->params['bot_name']);
        $message = null;
        try {
            //Получаем сообщение
            $bot->handle();
            $message = \GuzzleHttp\json_decode($bot->getCustomInput());
        } catch (TelegramException $e) {
            return ['status' => 'error', 'description' => $e->getMessage()];
        }

        if (Commands::parseMessage($message))
            return ['status' => 'success'];

        return ['status' => 'error', 'description' => 'Message is broken'];
    }

}

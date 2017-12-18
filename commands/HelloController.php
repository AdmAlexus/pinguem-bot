<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Longman\TelegramBot\Telegram;
use Yii;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {

    }

    public function actionSetWebhook() {
        $bot = new Telegram(Yii::$app->params['botToken'], 'pinguem_bot');
        $bot->setWebhook('https://example.com/', ['certificate' => Yii::$app->basePath . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'wildcard.example.com.pem']);
    }

}
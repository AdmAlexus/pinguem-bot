<?php

namespace app\models;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\sphinx\Query;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 14.08.17
 * Time: 16:40
 */

class Commands extends Model {

    /**
     * @var $from_id string
     */
    public $from_id;

    /**
     * @var $from_first_name string
     */
    public $from_first_name;

    /**
     * @var $from_last_name string
     */
    public $from_last_name;

    /**
     * @var $mediaType string
     */
    public $media_type = 'text';


    public static function parseMessage($message) {
        if ($message !== null) {
            //В сообщении должны быть параметры: text и from->id
            if (!isset($message->message->from->id))
                return false;

            if (isset($message->message->text) && strlen($message->message->text) > 0) {
                $commandsModel = new Commands();
                $commandsModel->from_id = $message->message->from->id;
                if (isset($message->message->from->first_name))
                    $commandsModel->from_first_name = $message->message->from->first_name;
                if (isset($message->message->from->last_name))
                    $commandsModel->from_last_name = $message->message->from->last_name;

                //Разбиваем команду на части: /(start)(@pinguem-bot) (текст)
                if (preg_match('/\/([A-Za-z0-9_-]+)(@' . Yii::$app->params['bot_name'] . '|)( .*|)/', strtolower($message->message->text), $matches)) {
                    if ($matches[2] === '' || $matches[2] == '@' . Yii::$app->params['bot_name']) {
                        $command = $matches[1];
                        try {
                            //Передаём обработку команды модели Commands
                            $commandsModel->$command(trim($matches[3]));
                        } catch (NotFoundHttpException $e) {
                            //return ['status' => 'error', 'description' => $e->getMessage()];
                            return false;
                        }

                        return true;
                    }
                } else { //если была отправлена не команда, а просто текст, вызываем соответствующую функцию
                    if (isset($message->message->text)) {
                        $commandsModel->rawText($message->message->text);
                        return true;
                    }
                }
            } elseif (isset($message->message->document->file_id)) {
                $commandsModel = new Commands();
                $commandsModel->from_id = $message->message->from->id;
                if (isset($message->message->from->first_name))
                    $commandsModel->from_first_name = $message->message->from->first_name;
                if (isset($message->message->from->last_name))
                    $commandsModel->from_last_name = $message->message->from->last_name;
                $commandsModel->media_type = 'document';

                $fileInfo = Request::getFile(['file_id' => $message->message->document->file_id]);

                if ($fileInfo && isset($fileInfo->result->file_path)) {
                    $file = file_get_contents('https://api.telegram.org/file/bot' . Yii::$app->params['botToken'] . '/' . $fileInfo->result->file_path);
                    $commandsModel->rawText($file);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Магия! :)
     *
     * @param string $name сюда попадает имя команды
     * @param array $arguments сюда попадают доп аргументы. В частности текст, который идёт за командой.
     * @return mixed
     */

    public function __call($name, $arguments) {
        $additionalArgs = [];

        //хук для заданий
        if (preg_match('/^quest[0-9]+$/', $name)) {
            $method = 'commandQuest';
            $additionalArgs[] = substr($name, 5);
        } else { //превращаем snake_case в camelCase
            $method = 'command' . preg_replace_callback_array([
                '/^[a-z]{1}/' =>
                    function ($matches) {
                        return strtoupper($matches[0]);
                    },
                '/_[a-z]{1}/' =>
                    function ($matches) {
                        return strtoupper($matches[0][1]);
                    },
            ], $name);
        }
        //если метод существует, то вызываем его
        if ($this->hasMethod($method)) {
            $result = call_user_func_array([$this, $method], ArrayHelper::merge($arguments, $additionalArgs));
            //если результат false, даём отбивку о несуществующей команде
            if (!$result) {
                $result = call_user_func_array([$this, 'defaultCommand'], ArrayHelper::merge($arguments, $additionalArgs));
            }
        } else { //иначе даём отбивку о несуществующей команде
            $additionalArgs[] = $name;
            $result = call_user_func_array([$this, 'defaultCommand'], ArrayHelper::merge($arguments, $additionalArgs));
        }

        //запишем команду в историю. Историю нужно уважать!
        $commandsHistory = new CommandsHistory();
        $commandsHistory->from_id = $this->from_id;
        $commandsHistory->command = $name;
        if ($arguments[0] !== '')
            $commandsHistory->text = $arguments[0];
        $commandsHistory->save();

        return $result;
    }

    /**
     * commandName($text) - формат для любой команды, где Name - имя команды, $text - переменная, содержащая текст после команды
     *
     * @return bool - если возвращает true, то команда была обработана.
     */

    public function commandStart() {
        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => "Бот Pinguem приветствует вас!
Подписывайтесь на <b>дайджест чата Pinguem</b> https://t.me/joinchat/AAAAAEN0K6rm4TjVmV58ug

/help - список того, что умеет бот.
"]);
        return true;
    }

    public function commandHelp() {
        $quests = Quests::find()->all();
        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => "<b>Доступные команды:</b>

/quests - список заданий, придуманных участниками чата Pinguem, на данный момент доступно " . count($quests) . " заданий. Наберите <code>/quest N</code> или <code>/questN</code>, где N - номер задания;
/links - ссылки на различные сервисы, помогающие начинающим криптологам.
/utils - некоторые утилиты по кодированию/декодированию/шифрованию.
/stats - статистика по чату Pinguem.

Подписывайтесь на <b>дайджест чата Pinguem</b> https://t.me/joinchat/AAAAAEN0K6rm4TjVmV58ug
"]);
        return true;
    }

    public function commandQuests() {
        $quests = Quests::find()->all();
        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => 'На данный момент доступно ' . count($quests) . ' заданий. Наберите <code>/quest N</code> или <code>/questN</code>, где N - номер задания.']);
        return true;
    }

    /**
     * @param $text
     * @param int $quest_id - задания дополнительно содержат id
     * @return bool
     */

    public function commandQuest($text, $quest_id = 0) {
        $quests = Quests::find()->all();

        if ($quest_id == 0)
            $quest_id = $text;

        if ($quest_id < 1 || $quest_id > count($quests))
            Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => 'На данный момент доступно только ' . count($quests) . ' заданий. Наберите <code>/quest N</code> или <code>/questN</code>, где N - номер задания.']);
        else {
            $content = '<b>Автор</b>: ' . $quests[$quest_id - 1]->author;
            if ($quests[$quest_id - 1]->comment)
                $content .= "
<b>Комметарий</b>: " . $quests[$quest_id - 1]->comment;
            $content .= "
<b>Описание</b>: " . $quests[$quest_id - 1]->description . "
";

            $hints = Hints::find()->where(['quest_id' => $quest_id])->all();
            if (count($hints) > 0)
                $content .= "
/hint - подсказка";

            $answer = Answers::find()->where(['quest_id' => $quest_id])->one();
            if ($answer)
                $content .= "
/answer - ответ";

            Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => $content]);
        }

        return true;
    }

    public function commandHint() {
        //Узнаем, какое задание было активировано последний раз
        $quest_id = $this->findLastQuest();

        $group = 'hints';
        $hints = Hints::find()->where(['not in', 'id', ItemsRead::find()->select(['item_id'])->where(['from_id' => $this->from_id, 'group' => $group])->column()])->andWhere(['quest_id' => $quest_id])->orderBy(['hint_id' => SORT_ASC])->all();

        if (count($hints) > 0) {
            Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => '<b>Подсказка №</b>' . $hints[0]->hint_id . ": " . $hints[0]->description]);

            if (count($hints) <= 1) {
                ItemsRead::deleteAll(['from_id' => $this->from_id, 'group' => $group]);
            } else {
                $add = new ItemsRead();
                $add->from_id = $this->from_id;
                $add->item_id = $hints[0]->id;
                $add->group = $group;
                $add->save();
            }
        } elseif ($quest_id == 0) {
            return false;
        } else {
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Подсказок для данного задания увы нет.']);
        }

        return true;
    }

    public function commandAnswer() {
        //Узнаем, какое задание было активировано последний раз
        $quest_id = $this->findLastQuest();

        $answer = Answers::find()->where(['quest_id' => $quest_id])->one();

        if ($answer) {
            Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => '<b>Ответ: </b>' . $answer->text]);
        } elseif ($quest_id == 0) {
            return false;
        } else {
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Ответа для данного задания увы нет.']);
        }

        return true;
    }

    public function commandSasvakhero() {
        $group = 'sasvakhero';
        $responses = Sasvakhero::find()->where(['not in', 'id', ItemsRead::find()->select(['item_id'])->where(['from_id' => $this->from_id, 'group' => $group])->column()])->all();

        $rand = rand(0, count($responses) - 1);

        Request::sendMessage(['chat_id' => $this->from_id, 'text' => $responses[$rand]->phrase]);

        if (count($responses) <= 1) {
            ItemsRead::deleteAll(['from_id' => $this->from_id, 'group' => $group]);
        } else {
            $add = new ItemsRead();
            $add->from_id = $this->from_id;
            $add->item_id = $responses[$rand]->id;
            $add->group = $group;
            $add->save();
        }

        return true;
    }

    public function commandSasvachero() {
        Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Сасвачеро? Мексиканский пирог?']);
        return true;
    }

    public function commandSasvak() {
        Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Даа... Есть такой парень.']);
        return true;
    }

    public function commandSasvac() {
        Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Ну что же вы имя легенды пишете неправильно?']);
        return true;
    }

    public function commandSasvack() {
        Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Сасвак. Джеймс Сасвак.']);
        return true;
    }

    public function commandAsarnm() {
        $group = 'asarnm';

        $fileIds = [];

        $readItems = ItemsRead::find()->select(['item_id'])->where(['from_id' => $this->from_id, 'group' => $group])->column();
        $filesCount = 3; //TODO: может, получится их посчитать
        for ($i = 1; $i <= $filesCount; ++$i) {
            $found = false;
            foreach ($readItems as $readItem) {
                if ($readItem == $i) {
                    $found = true;
                    break;
                }
            }

            if (!$found)
                $fileIds[] = $i;
        }
        $rand = rand(0, count($fileIds) - 1);

        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/asarnm' . $fileIds[$rand] . '.jpg')]);

        if (count($fileIds) <= 1) {
            ItemsRead::deleteAll(['from_id' => $this->from_id, 'group' => $group]);
        } else {
            $add = new ItemsRead();
            $add->from_id = $this->from_id;
            $add->item_id = $fileIds[$rand];
            $add->group = $group;
            $add->save();
        }

        return true;
    }

    public function commandAsanrm() {
        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/asanrm.jpg'), 'caption' => 'Ты не прошёл q2a']);
        return true;
    }

    public function commandAsaranm() {
        return $this->commandAsanrm();
    }

    public function commandTest() {
        $keyboard = new Keyboard([
            ['text' => 'yes'],
            ['text' => 'no'],
        ]);
        $keyboard->setOneTimeKeyboard(true);
        $keyboard->setResizeKeyboard(true);
        Request::sendPhoto([
            'chat_id' => $this->from_id,
            'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/test1.jpg'),
            'caption' => 'А ты точно тестировщик?',
            'reply_markup' => $keyboard,
        ]);
        return true;
    }

    public function commandYes() {
        $lastCommand = CommandsHistory::find()->where(['from_id' => $this->from_id])->orderBy(['id' => SORT_DESC])->one();
        if ($lastCommand && $lastCommand->command == 'test') {
            Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => "<b>Доступные тестовые команды:</b>

/asarnm
/sasvak
/sasvakhero
/kripta
/easy
и их неправильные варианты

/ira
/42
/die
/ping
/echo text
"]);
            return true;
        }

        return false;
    }

    public function commandNo() {
        $lastCommand = CommandsHistory::find()->where(['from_id' => $this->from_id])->orderBy(['id' => SORT_DESC])->one();
        if ($lastCommand && $lastCommand->command == 'test') {
            Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/testno.jpg'), 'caption' => 'Может, тогда останемся друзьями?']);
            return true;
        }

        return false;
    }

    public function commandNope() {
        return $this->commandNo();
    }

    public function commandEasy() {
        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/easy_title.png')]);

        $group = 'easy';

        $fileIds = [];

        $readItems = ItemsRead::find()->select(['item_id'])->where(['from_id' => $this->from_id, 'group' => $group])->column();
        $filesCount = 7; //TODO: может, получится их посчитать
        for ($i = 1; $i <= $filesCount; ++$i) {
            $found = false;
            foreach ($readItems as $readItem) {
                if ($readItem == $i) {
                    $found = true;
                    break;
                }
            }

            if (!$found)
                $fileIds[] = $i;
        }
        $rand = rand(0, count($fileIds) - 1);

        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/easy' . $fileIds[$rand] . '.jpg')]);

        if (count($fileIds) <= 1) {
            ItemsRead::deleteAll(['from_id' => $this->from_id, 'group' => $group]);
        } else {
            $add = new ItemsRead();
            $add->from_id = $this->from_id;
            $add->item_id = $fileIds[$rand];
            $add->group = $group;
            $add->save();
        }

        return true;
    }

    public function commandIzi() {
        return $this->commandEasy();
    }

    public function commandLinks() {
        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => "<b>Он-лайн сервисы для решения криптографических, стеганографических задач, кодировщики, калькуляторы</b>

http://base64.ru/ - простой кодировщик base64.
http://decodeit.ru/ - QR, штрихкод, ASCII, двоичный, base64, md5, sha, URL.
http://meyerweb.com/eric/tools/dencoder/ - URL декодер.
http://incoherency.co.uk/image-steganography/ - стеганография в изображениях
http://www.md5online.org/ - радужные таблицы MD5.
http://md5decrypt.net/en/ - тоже радужные таблицы.
http://rawpixels.net/ - работа с графикой, возможность визуализировать бинарные данные.
http://questhint.ru/ - всё о шифрах, алфавитах и не только.
http://www.dcode.fr/tools-list - многое о многом.
"]);
        return true;
    }

    public function command42() {
        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/42.jpg'), 'caption' => 'What is the meaning of life, the universe and everything? *42* Douglas Adams, the only person who knew what this question really was about is now dead, unfortunately. So now you might wonder what the meaning of death is…']);
        return true;
    }

    public function commandKripta() {
        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/kripta.jpg'), 'caption' => 'Криптаааааааа!']);
        return true;
    }

    public function commandCripta() {
        return $this->commandKripta();
    }

    public function commandCrypta() {
        return $this->commandKripta();
    }

    public function commandKrypta() {
        return $this->commandKripta();
    }

    public function commandDie() {
        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/die.jpg'), 'caption' => 'Сам подыхай, кусок мяса! И поцелуй мой блестящий зад!']);
        return true;
    }

    public function commandIra() {
        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/ira.png')]);
        return true;
    }

    public function commandPing() {
        Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'pong']);
        return true;
    }

    public function commandBin2ascii($string) {
        if ($string == '') {
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Вводите поочерёдно строки или отправляйте файлы для декодирования']);
            return true;
        }

        $string = preg_replace(['/[ \t]*/'], [''], $string);

        $result = '';
        if (!preg_match('/^[01]+$/', $string) || strlen($string) % 8 != 0)
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Некорректная строка']);
        else {
            for ($i = 0; $i < strlen($string); $i += 8) {
                $char = substr($string, $i, 8);
                $result .= chr(bindec($char));
            }

            switch ($this->media_type) {
                case 'text':
                    Request::sendMessage(['chat_id' => $this->from_id, 'text' => base64_encode($string)]);
                    break;
                case 'document':
                    $stream = tmpfile();
                    fwrite($stream, $result);
                    Request::sendDocument(['chat_id' => $this->from_id, 'document' => $stream]);
                    break;
            }
        }

        return true;
    }

    public function commandMd5($string) {
        if ($string == '') {
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Вводите поочерёдно строки или отправляйте файлы для получения хеша']);
            return true;
        }

        Request::sendMessage(['chat_id' => $this->from_id, 'text' => md5($string)]);
        return true;
    }

    public function commandBase64decode($string) {
        if ($string == '') {
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Вводите поочерёдно строки или отправляйте файлы для декодирования']);
            return true;
        }

        switch ($this->media_type) {
            case 'text':
                Request::sendMessage(['chat_id' => $this->from_id, 'text' => base64_encode($string)]);
                break;
            case 'document':
                $stream = tmpfile();
                fwrite($stream, base64_decode($string));
                Request::sendDocument(['chat_id' => $this->from_id, 'document' => $stream]);
                break;
        }

        return true;
    }

    public function commandBase64encode($string) {
        if ($string == '') {
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Вводите поочерёдно строки или отправляйте файлы для кодирования']);
            return true;
        }

        switch ($this->media_type) {
            case 'text':
                Request::sendMessage(['chat_id' => $this->from_id, 'text' => base64_encode($string)]);
                break;
            case 'document':
                $stream = tmpfile();
                fwrite($stream, base64_encode($string));
                Request::sendDocument(['chat_id' => $this->from_id, 'document' => $stream]);
                break;
        }

        return true;
    }

    public function commandEcho($text) {
        Request::sendMessage(['chat_id' => $this->from_id, 'text' => $text]);
        return true;
    }

    public function commandUtils() {
        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => "<b>Некоторые утилиты:</b>

/bin2ascii - превращает бинарный код в ASCII-строку (011100110110000101110011011101100110000101101011  -> sasvak). Пробелы во входящих данных игнорируются.
/md5 - вычисляет md5 от строки (sasvak -> 715a9dff780edf84a9cc91e50dd6bb0f);
/base64decode - вычисляет base64 от строки (c2FzdmFr -> sasvak);
/base64encode - вычисляет строку по её base64 представлению (sasvak -> c2FzdmFr).

Команды можно вводить как в одну строку с данными, например <code>/md5 sasvak</code>, так и набрав команду, а после ответа данные. Данные можно вводить неограниченное количество раз до ввода следующей команды.
"]);
        return true;
    }

    public function commandStats() {
        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => "<b>Статистика чата:</b>

/stats_flooders - топ флудеров;
/stats_words - топ используемых слов;
/stats_word - сколько раз было использовано слово.
"]);
        return true;
    }

    //TODO: удалять сообщения, которых нет в истории чата
    public function commandStatsFlooders() {
        $messagesCount = History::find()->count();

        $content = "<b>Всего сообщений в чате</b>: " . $messagesCount . "

<b>Топовая статистика по пользователям:</b>
";

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand('SELECT `from_id`, COUNT(`from_id`) AS `count` FROM (SELECT `from_id`, `group`, COUNT(`group`) FROM (SELECT `message_id`, `from_id`, IF (@prev = `from_id`, @group, @group := @group + 1) AS `group`, @prev := `from_id` AS `prev` FROM `history` JOIN (SELECT @prev := 0, @group := 0) AS `t1` WHERE `text` NOT REGEXP \'^/[a-zA-Z0-9_-]+\' OR `text` LIKE \'/me%\') AS `t2` GROUP BY `group`) AS `t3` GROUP BY `from_id` ORDER BY `count` DESC LIMIT 20');
        $messagesByUsersCount = $command->queryAll();

        $i = 1;
        foreach ($messagesByUsersCount as $item) {
            $historyUser = History::find()->where(['from_id' => $item['from_id']])->orderBy(['message_id' => SORT_DESC])->limit(1)->all();

            $first_name = $historyUser[0]->from_first_name;
            $last_name = $historyUser[0]->from_last_name;

            if ($item['from_id'] == 17059306)
                $last_name .= '(@ALEX_GAV)';
            $content .= "$i. <b>" . $item['count'] . "</b> <i>сообщ.</i>: " . $first_name . (($last_name) ? ' ' . $last_name : '') ."
";
            ++$i;
        }

        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => $content]);

        return true;
    }

    public function commandStatsWords() {
        $limit = 30;

        $content = "<b>Самые часто используемые слова:</b>

";

        $words = WordsFrequency::find()->orderBy(['frequency' => SORT_DESC])->limit($limit)->all();
        $i = 1;
        foreach ($words as $word) {
            $ending = 'раз';
            if (preg_match('/[^1][2-4]$/', $word->frequency))
                $ending = 'раза';
            $content .= $i . '. <b>' . $word->frequency . "</b> $ending: " . $word->word . "\n";
            ++$i;
        }

        Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => $content]);

        return true;
    }

    public function commandStatsWord($string) {
        if ($string == '') {
            Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Вводите поочерёдно слова, для которых нужно отобразить статистику']);
            return true;
        } elseif (preg_match_all('/([^ \t\n\r]+)[ \t\n\r]*/', $string, $matches)) {
            $content = '';

            foreach ($matches[1] as $match) {
                $query = new Query();
                $rows = $query->from('pinguem')->match($match)->showMeta(true)->search();
                $content .= "Слово <b>$match</b> встречается в чате " . $rows['meta']['hits[0]'] . " раз.
";
            }
            Request::sendMessage(['chat_id' => $this->from_id, 'parse_mode' => 'HTML', 'text' => $content]);
            return true;
        }
        return false;
    }

    public function commandWhoami() {
        Request::sendPhoto(['chat_id' => $this->from_id, 'photo' => Request::encodeFile(Yii::$app->basePath . '/web/img/asanrm.jpg'), 'caption' => 'Ты ' . $this->from_first_name . (($this->from_last_name != '') ? ' ' . $this->from_last_name : '') . '!']);
        return true;
    }

    public function commandMe() {
        return true;
    }


    /**
     * Ищет в истории последнее активированное задание
     *
     * @return bool|int|mixed|string
     */

    private function findLastQuest() {
        $lastCommands = CommandsHistory::find()->where(['from_id' => $this->from_id])->orderBy(['id' => SORT_DESC])->all();
        $quest_id = 0;
        foreach ($lastCommands as $lastCommand) {
            if (preg_match('/^quest[0-9]+$/', $lastCommand->command)) {
                $quest_id = substr($lastCommand->command, 5);
                break;
            } elseif ($lastCommand->command == 'quest' && preg_match('/^[0-9]+$/', $lastCommand->text)) {
                $quest_id = $lastCommand->text;
                break;
            }
        }

        return $quest_id;
    }

    /**
     * обрабатывает текст, отправленный боту без команды
     *
     * @param $text
     */

    public function rawText($text) {
        $lastCommand = CommandsHistory::find()->where(['from_id' => $this->from_id])->orderBy(['id' => SORT_DESC])->one();
        if ($lastCommand) {
            switch ($lastCommand->command) {
                case 'bin2ascii':
                    $this->commandBin2ascii($text);
                    return;
                case 'md5':
                    $this->commandMd5($text);
                    return;
                case 'base64decode':
                    $this->commandBase64decode($text);
                    return;
                case 'base64encode':
                    $this->commandBase64encode($text);
                    return;
                case 'stats_word':
                    $this->commandStatsWord($text);
                    return;
            }
        }

        switch ($text) {
            case 'yes':
                $this->commandYes();
                break;
            case 'no':
                $this->commandNo();
                break;
        }
    }

    /**
     * Даёт отбивку о несуществующей команде
     *
     * @param null $command
     * @return bool
     */

    public function defaultCommand($command = null) {
        Request::sendMessage(['chat_id' => $this->from_id, 'text' => 'Простите, но такой команды я не знаю...']);
        if ($command !== null) {
            $customCommand = new CustomCommands();
            $customCommand->command = $command;
            $customCommand->save();
        }

        return true;
    }

}

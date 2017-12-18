<?php
/**
 * Created by PHPStorm.
 * User: daemon
 * Date: 22.08.17
 * Time: 16:38
 */

namespace app\commands;


use app\models\History;
use app\models\StopLemmas;
use app\models\WordsFrequency;
use Rs\JsonLines\JsonLines;
use Yii;
use yii\console\Controller;
use yii\db\IntegrityException;
use yii\sphinx\Query;

class HistoryController extends Controller
{

    public function actionParse()
    {
        $json = (new JsonLines())->delineFromFile(Yii::$app->getBasePath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'history' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'PinguemChat.jsonl');
        $json_array = json_decode($json);

        foreach ($json_array as $item) {
            if ($item->event == "message" && $item->service == false && (isset($item->text) || isset($item->media->type))) {
                try {
                    $historyItem = new History();
                    $historyItem->message_id = History::getIntegerFromHexStringLittleEndian(substr($item->id, 16, 16));
                    $historyItem->chat_id = $item->to->peer_id;
                    $historyItem->chat_title = $item->to->title;
                    $historyItem->from_id = $item->from->peer_id;
                    $historyItem->from_first_name = $item->from->first_name;
                    if ($item->from->last_name != '')
                        $historyItem->from_last_name = $item->from->last_name;
                    $historyItem->date = date('Y-m-d H:i:s', $item->date);
                    if (isset($item->text)) {
                        $historyItem->type = 'text';
                        $historyItem->text = $item->text;
                    }
                    $historyItem->status = 'unread';
                    $historyItem->save();
                } catch (IntegrityException $e) {}
            }
        }
    }

    public function actionSphinxAnalyze() {
        WordsFrequency::updateAll(['validated' => false]);
        $data = explode("\n", file_get_contents(Yii::$app->getBasePath() . DIRECTORY_SEPARATOR . '../' . DIRECTORY_SEPARATOR . 'wordfreq.txt'));
        foreach ($data as $word) {
            if ($word == '')
                continue;

            $query = new Query();
            $rows = $query->from('pinguem')->match($word)->showMeta(true)->search();

            $lemma = $rows['meta']['keyword[0]'];
            $realFrequency = $rows['meta']['hits[0]'];
            $stopLemmaExists = StopLemmas::find()->where(['lemma' => $lemma])->all();
            if (count($stopLemmaExists) > 0)
                continue;

            $foundLemma = WordsFrequency::find()->where(['lemma' => $lemma])->all();
            if (count($foundLemma) > 0) {
                $wordModel = $foundLemma[0];
                if ($wordModel->validated)
                    continue;

                $wordModel->frequency = $realFrequency;
                $wordModel->validated = true;
                $wordModel->save();
            } else {
                $wordModel = new WordsFrequency();
                $wordModel->word = $word;
                $wordModel->frequency = $realFrequency;
                $wordModel->lemma = $lemma;
                $wordModel->save();
            }
        }

        WordsFrequency::deleteAll(['validated' => false]);
    }

}
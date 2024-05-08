<?php

namespace app\commands;

use app\helpers\TranslateHelper;
use codemix\excelmessage\ExcelMessageController;
use Exception;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use Yii;
use app\components\PhpMessageSource;

class MessageController extends \yii\console\controllers\MessageController
{

    public $defaultAction = 'help';

    public $from = 'en';

    public $to = 'ru';

    public $apikey = null;

    public function options($actionID) {
        return array_merge(parent::options($actionID), [
            'from',
            'to',
            'apikey',
        ]);
    }

    /**
     * Help message
     */
    public function actionHelp() {
        echo "Search messages\n";
        echo "./yii message/extract\n";
        echo "Translate all messages\n";
        echo "./yii message/translate\n";
    }

    /**
     * Test text translation
     */
    public function actionTest($text) {
        if($this->apikey){
            \Yii::$app->params['google_api_key'] = $this->apikey;
        }
        if($text == '!' && !file_exists(__DIR__ . '/../../config/tr/countries.php')){
            $countries = include_once __DIR__ . '/../../config/ru/countries.php';
            foreach($countries as $i => $country){
                $countries[$i]['name'] = $new = TranslateHelper::translate('ru', 'tr', $old = $country['name']);
                echo "$old => $new \n";
            }
            file_put_contents(__DIR__ . '/../../config/tr/countries.php', var_export($countries, true));
            exit;
        }
        echo TranslateHelper::translate($this->from, $this->to, $text) . "\n";
    }

    public function actionExtract($configFile = 'config/message.php') {
        $this->initConfig($configFile);
        if(!empty($this->config['sourcePaths'])){
            $files = [];
            foreach($this->config['sourcePaths'] as $sourcePath){
                $files = array_merge($files, FileHelper::findFiles(realpath($sourcePath), $this->config));
            }
        }else{
            $files = FileHelper::findFiles(realpath($this->config['sourcePath']), $this->config);
        }

        $messages = [];
        foreach($files as $file){
            $messages = array_merge_recursive($messages, $this->extractMessages($file, $this->config['translator'], $this->config['ignoreCategories']));
        }

        $catalog = isset($this->config['catalog']) ? $this->config['catalog'] : 'messages';

        if(in_array($this->config['format'], ['php', 'po'])){
            foreach($this->config['languages'] as $language){
                $dir = $this->config['messagePath'] . DIRECTORY_SEPARATOR . $language;
                if(!is_dir($dir) && !@mkdir($dir)){
                    throw new Exception("Directory '{$dir}' can not be created.");
                }
                if($this->config['format'] === 'po'){
                    $this->saveMessagesToPO($messages, $dir, $this->config['overwrite'], $this->config['removeUnused'], $this->config['sort'], $catalog, $this->config['markUnused']);
                }else{
                    $this->saveMessagesToPHP($messages, $dir, $this->config['overwrite'], $this->config['removeUnused'], $this->config['sort'], $this->config['markUnused']);
                }
            }
        }else if($this->config['format'] === 'db'){
            /** @var Connection $db */
            $db = Instance::ensure($this->config['db'], Connection::class);
            $sourceMessageTable = isset($this->config['sourceMessageTable']) ? $this->config['sourceMessageTable'] : '{{%source_message}}';
            $messageTable = isset($this->config['messageTable']) ? $this->config['messageTable'] : '{{%message}}';
            $this->saveMessagesToDb(
                $messages,
                $db,
                $sourceMessageTable,
                $messageTable,
                $this->config['removeUnused'],
                $this->config['languages'],
                $this->config['markUnused']
            );
        }else if($this->config['format'] === 'pot'){
            $this->saveMessagesToPOT($messages, $this->config['messagePath'], $catalog);
        }
    }

    /**
     * Translate empty values in translations
     *
     * @throws Exception
     */
    public function actionTranslate($configFile = 'config/message.php') {
        $this->initConfig($configFile);
        Yii::$app->cache->flush();
        $fileTranslations = Instance::ensure([
            'class'          => PhpMessageSource::class,
            'basePath'       => '@app/messages',
            'sourceLanguage' => 'en-US',
        ], PhpMessageSource::class);
        $messagesFile = [];
        foreach(['site', 'admin', 'account'] as $category){
            foreach(['ru', 'tr', 'de'] as $language){
                $messagesFile[$category][$language] = $fileTranslations->loadMessages($category, $language);
            }
        }
        $updated = false;
        $oldMessages = $messagesFile;
        foreach($messagesFile as $category => $messagesLang){
            foreach($messagesLang as $language => $messages){
                foreach($messages as $key => $value){
                    if(empty($value)){
                        $parts = false;
                        $updated = true;
                        $tText = $key;
                        $placeholders = [];
                        if(preg_match_all('|\{.*?\}|is', $key, $m)){
                            $parts = $m[0];
                            foreach($parts as $k => $part){
                                $placeholders[$k] = '{' . $k . '}';
                            }
                            $tText = str_replace($parts, $placeholders, $tText);
                        }
                        $messagesFile[$category][$language][$key] = TranslateHelper::translate('en', $language, $tText);
                        if($parts){
                            $messagesFile[$category][$language][$key] = str_replace($placeholders, $parts, $messagesFile[$category][$language][$key]);
                        }
                        echo "'$key' => '{$messagesFile[$category][$language][$key]}',\n";
                    }
                }
            }
        }
        if($updated){
            foreach($messagesFile as $category => $messagesLang){
                foreach($messagesLang as $language => $messages){
                    $file = dirname(__DIR__) . '/messages/' . $language . '/' . $category . '.php';
                    $this->saveMessagesFile($file, $messages);
                }
            }
        }
        Yii::$app->cache->flush();
    }

    private function saveMessagesFile($file, $messages) {
        ksort($messages, SORT_STRING | SORT_FLAG_CASE);
        $messages = var_export($messages, true);
        $messages = preg_replace('|^.*?\(\n|is', '', $messages);
        $messages = preg_replace('|\)$|is', '', $messages);
        $messages = '<?php' . "\n\nreturn [\n" . $messages . "];\n";
        file_put_contents($file, $messages);
    }

    /**
     * Clear values in translation file
     *
     * @param string $file
     */
    public function actionClean($file) {
        $files = func_get_args();
        foreach($files as $file){
            echo "\n" . $file . "\n\n";
            if(!preg_match('|/messages/([a-z]{2})/|is', $file, $math) || !file_exists($file)){
                echo "File $file not exists or not translation file";
                exit;
            }
            $messages = require_once($file);
            $messages = array_fill_keys(array_keys($messages), '');
            $this->saveMessagesFile($file, $messages);
        }
    }

    protected function saveMessagesToPHP($messages, $dirName, $overwrite, $removeUnused, $sort, $markUnused) {
        foreach($messages as $category => $msgs){
            $file = str_replace('\\', '/', "$dirName/$category.php");
            $path = dirname($file);
            FileHelper::createDirectory($path);
            $msgs = array_values(array_unique($msgs));
            $coloredFileName = Console::ansiFormat($file, [Console::FG_CYAN]);
            $this->stdout("Saving messages to $coloredFileName...\n");
            $this->saveMessagesCategoryToPHP($msgs, $file, $overwrite, $removeUnused, $sort, $category, $markUnused);
        }

        if($removeUnused){
            // Do not remove files if in ignoreCategories list
            foreach($this->config['ignoreCategories'] as $cat){
                $messages[$cat] = [];
            }
            $this->deleteUnusedPhpMessageFiles(array_keys($messages), $dirName);
        }
    }

    private function deleteUnusedPhpMessageFiles($existingCategories, $dirName) {
        $messageFiles = FileHelper::findFiles($dirName);
        foreach($messageFiles as $messageFile){
            $categoryFileName = str_replace($dirName, '', $messageFile);
            $categoryFileName = ltrim($categoryFileName, DIRECTORY_SEPARATOR);
            $category = preg_replace('#\.php$#', '', $categoryFileName);
            $category = str_replace(DIRECTORY_SEPARATOR, '/', $category);

            if(!in_array($category, $existingCategories, true)){
                unlink($messageFile);
            }
        }
    }

    /**
     * Export all messages to XLS file
     */
    public function actionExport() {
        $excel = new ExcelMessageController('excel-message', $this->module);
        $excel->actionExport('config/message.php', ROOT_DIR, 'all');
    }

    /**
     * Import all messages to XLS file
     */
    public function actionImport() {
        $excel = new ExcelMessageController('excel-message', $this->module);
        $excel->actionImport('config/message.php', ROOT_DIR);
    }

}

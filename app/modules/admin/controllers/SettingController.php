<?php

namespace app\modules\admin\controllers;

use app\components\LanguageSelector;
use app\models\behaviors\TranslateBehavior;
use app\models\forms\ImportForm;
use app\models\SettingModel;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii2mod\settings\controllers\DefaultController;
use yii2mod\toggle\actions\ToggleAction;

class SettingController extends DefaultController
{

    public $indexView = '@root/views/admin/setting/index';

    public $createView = '@root/views/admin/setting/create';

    public $updateView = '@root/views/admin/setting/update';

    public $searchClass = 'app\models\search\SettingSearch';

    public $modelClass = 'app\models\SettingModel';

    public function actions(): array {
        return ArrayHelper::merge(parent::actions(), [
            'toggle' => [
                'class'      => ToggleAction::class,
                'modelClass' => SettingModel::class,
                'preProcess' => function ($model) {
                    if ($model->system) {
                        $model->status = $model->getOldAttribute('status');
                    }
                },
            ],
        ]);
    }

    public function behaviors(): array {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class'        => AccessControl::class,
                'denyCallback' => function ($user) {
                    if (Yii::$app->user->isGuest) {
                        $this->response->redirect(['/site/login', 'ret' => $this->request->url]);
                    } else if (Yii::$app->controller->id != 'default') {
                        $this->response->redirect(['/cp']);
                    } else {
                        $this->response->redirect('/');
                    }
                },
                'rules'        => [
                    [
                        'allow' => true,
                        'roles' => ['admin', $this->id],
                    ],
                ],
            ],
        ]);
    }

    public function actionExport() {
        $model = new ImportForm();
        if (Yii::$app->request->isPost) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $languages = LanguageSelector::codes();
            $query = SettingModel::find()->asArray()->orderBy(['id' => SORT_ASC])->asArray();
            $sheet->setCellValue('A1', 'type');
            $sheet->getCell('A1')->getStyle()->getFont()->setBold(true);
            $sheet->setCellValue('B1', 'section');
            $sheet->getCell('B1')->getStyle()->getFont()->setBold(true);
            $sheet->setCellValue('C1', 'key');
            $sheet->getCell('C1')->getStyle()->getFont()->setBold(true);
            $sheet->setCellValue('D1', 'value');
            $sheet->getCell('D1')->getStyle()->getFont()->setBold(true);
            $row = 1;
            foreach ($languages as $i => $lang) {
                $sheet->setCellValue(($col = chr(ord('A') + $i + 4)) . '1', 'value:' . $lang);
                $sheet->getCell($col . '1')->getStyle()->getFont()->setBold(true);
            }
            foreach ($query->batch() as $settings) {
                foreach ($settings as $setting) {
                    $row++;
                    $sheet->setCellValue('A' . $row, $setting['type']);
                    $sheet->getColumnDimension('A')->setAutoSize(true);
                    $sheet->setCellValue('B' . $row, $setting['section']);
                    $sheet->getColumnDimension('B')->setAutoSize(true);
                    $sheet->setCellValue('C' . $row, $setting['key']);
                    $sheet->getColumnDimension('C')->setAutoSize(true);
                    $sheet->setCellValue('D' . $row, $setting['type'] == 'tstring' ? '' : $setting['value']);
                    $sheet->getColumnDimension('D')->setAutoSize(true);
                    if ($setting['type'] == 'tstring') {
                        foreach ($languages as $i => $lang) {
                            $sheet->setCellValue(($col = chr(ord('A') + $i + 4)) . $row, TranslateBehavior::tGet($setting['value'], $lang, true));
                            $sheet->getColumnDimension($col)->setAutoSize(true);
                        }
                    }

                }
            }
            $writer = new XlsWriter($spreadsheet);
            Yii::$app->response->headers->add('Content-type', 'application/vnd.ms-excel');
            Yii::$app->response->headers->add('Cache-Control', 'max-age=0');
            Yii::$app->response->headers->add('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
            Yii::$app->response->headers->add('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            Yii::$app->response->headers->add('Cache-Control', 'cache, must-revalidate');
            Yii::$app->response->headers->add('Pragma', 'public');
            Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="Settings.' . Yii::$app->formatter->asDatetime(time()) . '.xls"');
            ob_start();
            $writer->save('php://output');
            $this->layout = false;
            $content = ob_get_contents();
            ob_clean();
            return $content;
        }
        return $this->render('export', ['model' => $model]);
    }

    public function actionImport() {
        $model = new ImportForm();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->validate()) {
                $reader = new XlsReader();
                if ($reader->canRead($model->file->tempName) && $spreadsheet = $reader->load($model->file->tempName)) {
                    $sheet = $spreadsheet->getActiveSheet();
                    $languages = LanguageSelector::codes();
                    $c = count($languages);
                    $max = $sheet->getHighestRowAndColumn();
                    $columns = [];
                    $row = 1;
                    // 1. check headers
                    if ($sheet->getCell('A1')->getValue() == 'type') {
                        $headers = true;
                        for ($col = 'A'; $col <= $max['column']; $col++) {
                            $columns[$sheet->getCell($col . '1')->getValue()] = $col;
                        }
                        $row++;
                    } else {
                        $columns['type'] = 'A';
                        $columns['section'] = 'B';
                        $columns['key'] = 'C';
                        $columns['value'] = 'D';
                        foreach ($languages as $i => $lang) {
                            $columns['value:' . $lang] = chr(ord('A') + $i + 4);
                        }
                    }
                    $errors = false;
                    if (empty($columns['type'])) {
                        Yii::$app->session->addFlash('error', Yii::t('site', 'Column {column} required', ['column' => 'type']));
                        $errors = true;
                    }
                    if (empty($columns['section'])) {
                        Yii::$app->session->addFlash('error', Yii::t('site', 'Column {column} required', ['column' => 'section']));
                        $errors = true;
                    }
                    if (empty($columns['key'])) {
                        Yii::$app->session->addFlash('error', Yii::t('site', 'Column {column} required', ['column' => 'key']));
                        $errors = true;
                    }
                    if (empty($columns['value'])) {
                        Yii::$app->session->addFlash('error', Yii::t('site', 'Column {column} required', ['column' => 'value']));
                        $errors = true;
                    }
                    foreach ($languages as $i => $lang) {
                        if (empty($columns['value:' . $lang])) {
                            Yii::$app->session->addFlash('error', Yii::t('site', 'Column {column} required', ['column' => 'value:' . $lang]));
                            $errors = true;
                        }
                    }
                    if ($errors) {
                        return $this->redirect('index');
                    }
                    while ($row <= $max['row']) {
                        $type = strtolower(trim($sheet->getCell($columns['type'] . $row)->getValue()));
                        $section = trim($sheet->getCell($columns['section'] . $row)->getValue());
                        $key = trim($sheet->getCell($columns['key'] . $row)->getValue());
                        if ($type == 'tstring') {
                            $value = [];
                            foreach ($languages as $i => $lang) {
                                $value[$lang] = $sheet->getCell($columns['value:' . $lang] . $row)->getValue();
                            }
                        } else {
                            $value = trim($sheet->getCell($columns['value'] . $row)->getValue());
                        }
                        SettingModel::updateAll([
                            'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
                            'type'  => $type,
                        ], [
                            'section' => $section,
                            'key'     => $key,
                        ]);
                        $row++;
                    }
                }
            }
        }
        Yii::$app->settings->invalidateCache();
        return $this->redirect('index');
    }

}
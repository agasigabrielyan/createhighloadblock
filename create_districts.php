<?php
// Подключение необходимых модулей Bitrix
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Entity;
use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock\HighloadBlockLangTable;

// Подключение модулей для работы с инфоблоками и Highload-блоками
Loader::includeModule('iblock');
Loader::includeModule('highloadblock');

// Определение класса для миграции создания Highload-блока и полей
class CreateHighloadBlock
{
    public $fieldName;
    public $fieldNameValue;
    public $fieldNameValueEnglish;
    public $hlBlockNameRu;
    public $hlBlockNameEn;

    // Конструктор класса, принимающий названия полей и их значения
    public function __construct($fieldName, $fieldNameValue, $fieldNameValueEnglish)
    {
        $this->fieldName = $fieldName;
        $this->fieldNameValue = $fieldNameValue;
        $this->fieldNameValueEnglish = $fieldNameValue;
    }

    // Метод для выполнения миграции
    public function up()
    {
        // Данные для создания Highload-блока
        $hlblockData = array(
            'NAME' => 'Districts',
            'TABLE_NAME' => 'b_hlbd_districts',
        );

        // Создание Highload-блока и получение его ID
        $hlblockId = self::createHighloadBlock($hlblockData);

        // Добавление языковых наименований
        HighloadBlockLangTable::add([
            'ID' => $hlblockId,
            'LID' => 'ru',
            'NAME' => $hlblockData['NAME'],
        ]);

        HighloadBlockLangTable::add([
            'ID' => $hlblockId,
            'LID' => 'en',
            'NAME' => $hlblockData['NAME'],
        ]);

        // Если блок успешно создан, создаем необходимое поле
        if ($hlblockId) {
            self::createField($hlblockId, 'UF_DISTRICT', 'Федеральный округ', 'Federal district', array('MANDATORY' => 'Y'), $type = "string");
        }
    }

    // Метод для создания Highload-блока и возврата его ID
    public function createHighloadBlock($data)
    {
        $result = Bitrix\Highloadblock\HighloadBlockTable::add($data);
        return $result->getId();
    }

    // Метод для создания пользовательского поля в Highload-блоке
    public function createField($hlblockId, $fieldName, $fieldNameValue, $fieldNameValueEnglish, $fieldData, $type)
    {
        $UFObject = "HLBLOCK_" . $hlblockId;

        // Данные для создания пользовательского поля
        $arHighloadFields = Array(
            'UF_CART_ID' => Array(
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => $fieldName,
                'USER_TYPE_ID' => $type,
                'MANDATORY' => 'Y',
                "EDIT_FORM_LABEL" => Array('ru' => $fieldNameValue, 'en' => $fieldNameValueEnglish),
                "LIST_COLUMN_LABEL" => Array('ru' => $fieldNameValue, 'en' => $fieldNameValueEnglish),
                "LIST_FILTER_LABEL" => Array('ru' => $fieldNameValue, 'en' => $fieldNameValueEnglish),
                "ERROR_MESSAGE" => Array('ru' => '', 'en' => ''),
                "HELP_MESSAGE" => Array('ru' => '', 'en' => ''),
            )
        );

        // Массив для хранения ID созданных полей
        $arSavedFieldsRes = Array();

        // Создание пользовательских полей
        foreach ($arHighloadFields as $arField) {
            $obUserField = new CUserTypeEntity;
            $ID = $obUserField->Add($arField);
            $arSavedFieldsRes[] = $ID;
        }

        return $arSavedFieldsRes;
    }
}

// Создание экземпляра класса миграции с указанными значениями полей
$newDisctrictMigration = new CreateHighloadBlock(
    "UF_DISCTRICT",
    "Федеральный округ",
    "Federal district"
);

// Выполнение миграции
$newDisctrictMigration->up();
?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Entity;
use \Bitrix\Main\Loader;


Loader::includeModule('iblock');
Loader::includeModule('highloadblock');


class CreateHighloadBlock
{
    public $fieldName;
    public $fieldNameValue;
    public $fieldNameValueEnglish;

    public function __construct( $fieldName, $fieldNameValue, $fieldNameValueEnglish ) {
        $this->fieldName = $fieldName;
        $this->fieldNameValue = $fieldNameValue;
        $this->fieldNameValueEnglish = $fieldNameValue;
    }

    public function up()
    {
        $hlblockData = array(
            'NAME' => 'Districts',
            'TABLE_NAME' => 'b_hlbd_districts',
        );

        $hlblockId = self::createHighloadBlock($hlblockData);

        if ($hlblockId) {
            self::createField($hlblockId, 'UF_DISTRICT', 'Федеральный округ', 'Federal disctrict', array('MANDATORY' => 'Y'), $type="string");
        }
    }

    public function createHighloadBlock($data)
    {
        $result = Bitrix\Highloadblock\HighloadBlockTable::add($data);
        return $result->getId();
    }

    public function createField($hlblockId, $fieldName, $fieldNameValue, $fieldNameValueEnglish, $fieldData, $type)
    {
        $UFObject = "HLBLOCK_" . $hlblockId;

        $arHighloadFields = Array(
            'UF_CART_ID'=>Array(
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => $fieldName,
                'USER_TYPE_ID' => $type,
                'MANDATORY' => 'Y',
                "EDIT_FORM_LABEL" => Array('ru'=>$fieldNameValue, 'en'=>$fieldNameValueEnglish),
                "LIST_COLUMN_LABEL" => Array('ru'=>$fieldNameValue, 'en'=>$fieldNameValueEnglish),
                "LIST_FILTER_LABEL" => Array('ru'=>$fieldNameValue, 'en'=>$fieldNameValueEnglish),
                "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
            )
        );

        $arSavedFieldsRes = Array();
        foreach($arHighloadFields as $arField){
            $obUserField  = new CUserTypeEntity;
            $ID = $obUserField->Add($arField);
            $arSavedFieldsRes[] = $ID;
        }

        return $arSavedFieldsRes;
    }
}

$newDisctrictMigration = new CreateHighloadBlock(
    "UF_DISCTRICT",
    "Федеральный округ",
    "Federal district"
);

$newDisctrictMigration->up();

?>
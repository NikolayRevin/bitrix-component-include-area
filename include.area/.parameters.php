<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

if (! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (! Loader::includeModule("iblock"))
    return;

$arTypes = CIBlockParameters::GetIBlockTypes();

$arIBlocks = array();
$db_iblock = CIBlock::GetList(array(
    "SORT" => "ASC"
), array(
    "SITE_ID" => $_REQUEST["site"],
    "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")
));
while ($arRes = $db_iblock->Fetch()) {
    $arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("PTB_IBLOCK_DESC_LIST_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypes,
            "DEFAULT" => "",
            "REFRESH" => "Y"
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("PTB_IBLOCK_DESC_LIST_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y"
        ),
        "TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("PTB_IBLOCK_DESC_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => array(
                "RETURN" =>  Loc::getMessage("PTB_IBLOCK_DESC_TYPE_RETURN"),
                "TEMPLATE" => Loc::getMessage("PTB_IBLOCK_DESC_TYPE_TEMPLATE"),
            ),
            "DEFAULT" => "RETURN"
        ),
        "IBLOCK_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("PTB_IBLOCK_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => ''
        ),
        "ELEMENT_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("PTB_ELEMENT_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => ''
        ),
        "CACHE_TIME" => array(
            "DEFAULT" => 36000000
        )
    )
);
?>
<?
if (! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\Data\Cache;

class CPtbIncludeAreaComponent extends \CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {
        $arParams['IBLOCK_CODE'] = trim($arParams['IBLOCK_CODE']);
        $arParams['ELEMENT_CODE'] = trim($arParams['ELEMENT_CODE']);
        $arParams['IBLOCK_ID'] = (int) $arParams['IBLOCK_ID'];
        $arParams['CACHE_TIME'] = (int) $arParams['CACHE_TIME'];

        if ($arParams['IBLOCK_ID'] <= 0 && ! $arParams['IBLOCK_CODE']) {
            throw new SystemException('Not found iblock id or iblock code');
        }

        if (! $arParams['ELEMENT_CODE']) {
            throw new SystemException('Not found code of area');
        }

        return $arParams;
    }

    private function getIblockIdByCode($code)
    {
        $obCache = Cache::createInstance();
        $cacheDir = '/' . SITE_ID . '/ptb_iblocks_codes';

        $arFilter = array(
            "ACTIVE" => "Y",
            "SITE_ID" => SITE_ID,
            "CODE" => $code,
            "MIN_PERMISSION" => "R"
        );

        if ($obCache->initCache($this->arParams['CACHE_TIME'], md5(serialize($arFilter), $cacheDir))) {
            $result = $obCache->getVars();
        } elseif ($obCache->startDataCache($this->arParams['CACHE_TIME'], md5(serialize($arFilter)), $cacheDir)) {
            $result = 0;
            $rs = \CIBlock::GetList(array(), $arFilter, false);
            if ($ar = $rs->Fetch()) {
                $result = $ar["ID"];
                $obCache->endDataCache($result);
            } else {
                $obCache->abortDataCache();
            }
        }

        return $result;
    }

    public function executeComponent()
    {
        global $CACHE_MANAGER;

        $arParams = &$this->arParams;
        $arResult = &$this->arResult;

        if (! Loader::includeModule('iblock')) {
            throw new SystemException('Module iblock not installed.');
        }

        $arFilter = array(
            '=CODE' => $arParams['ELEMENT_CODE'],
            'ACTIVE' => 'Y',
            'IBLOCK_LID' => SITE_ID
        );

        if ($arParams['IBLOCK_ID'] > 0) {
            $arFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
        } else {
            $arFilter['IBLOCK_ID'] = $this->getIblockIdByCode($arParams['IBLOCK_CODE']);
        }

        $obCache = Cache::createInstance();
        $cacheDir = '/' . SITE_ID . '/ptb_include_area';

        if ($obCache->initCache($arParams['CACHE_TIME'], md5(serialize(array_merge((array) $arFilter, (array) $arParams))), $cacheDir)) {
            $arResult['TEXT'] = $obCache->getVars();
        } elseif ($obCache->startDataCache($arParams['CACHE_TIME'], md5(serialize(array_merge((array) $arFilter, (array) $arParams))), $cacheDir)) {
            $rs = \CIBlockElement::GetList(array(
                'ID' => 'DESC'
            ), $arFilter, false, array(
                'nTopCount' => 1
            ), array(
                'PREVIEW_TEXT'
            ));

            $arResult['TEXT'] = "";
            if ($arItem = $rs->Fetch()) {
                $arResult['TEXT'] = $arItem['PREVIEW_TEXT'];
                $CACHE_MANAGER->StartTagCache($cacheDir);
                $CACHE_MANAGER->RegisterTag('iblock_id_'.$arFilter['IBLOCK_ID']);
                $CACHE_MANAGER->EndTagCache();
                $obCache->endDataCache($arResult['TEXT']);
            } else {
                $obCache->abortDataCache();
            }
        }

        $this->includeComponentTemplate();

        return $arResult['TEXT'];
    }
}

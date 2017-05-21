# Bitrix: Включаемая область c данными из инфоблока

Как использовать:

1. Разместить компонент в папку /bitrix/components/YourPholderOfComponent.
2. Вставить компонент через редактор или в исходном коде.
3. Настроить: указать id или код инфоблока и символьный код элемента.

```

<?$APPLICATION->IncludeComponent(
  "YourPholderOfComponent:include.area",
  "",
  Array(
    "CACHE_TIME" => "36000000",
    "CACHE_TYPE" => "A",
    "ELEMENT_CODE" => "", // Символьный код элемента
    "IBLOCK_CODE" => "", // Код инфоблока
    "IBLOCK_ID" => "", // ID инфоблока
    "IBLOCK_TYPE" => "" // Тип инфоблока  
  )
);?>
```

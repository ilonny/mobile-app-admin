<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$arr = [
    'января',
    'феврал]',
    'марта',
    'апреля',
    'мая',
    'июня',
    'июля',
    'августа',
    'сентября',
    'октября',
    'ноября',
    'декабря'
  ];
  
  // Поскольку от 1 до 12, а в массиве, как мы знаем, отсчет идет от нуля (0 до 11),
  // то вычитаем 1 чтоб правильно выбрать уже из нашего массива.
if ($_GET['offset']){
    $offset = $_GET['offset'];
} else {
    $offset = 0;
}
$type = $_GET['type'];

if (\Bitrix\Main\Loader::includeModule('iblock')) {
    //IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
    $arSelect = Array(
        "ID",
        "IBLOCK_ID",
        "NAME",
        "IBLOCK_SECTION_ID",
        "PROPERTY_13",
        "PREVIEW_PICTURE",
        "PREVIEW_TEXT"
    /*, "DATE_ACTIVE_FROM","PROPERTY_*"*/);
    $arFilter = Array("IBLOCK_ID"=>IntVal(4), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_13_VALUE" => $type);
    $res = CIBlockElement::GetList(Array("ACTIVE_FROM" => "DESC", "SORT" => "ASC", "id" => "DESC" ), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    // var_dump($res);die();
    $result = $res->arResult;
    array_walk_recursive($result, function (&$val) { $val = strip_tags($val); });
    foreach($result as $key => $elem){
        $result[$key]['PREVIEW_PICTURE_SRC'] = 'https://harekrishna.ru'.CFile::GetPath($elem['PREVIEW_PICTURE']);
        $month = date('n', strtotime($elem['ACTIVE_FROM']))-1;
        $result[$key]['DATE_TEXT'] = date('d', strtotime($elem['ACTIVE_FROM'])).' '.$arr[$month].' '.date('Y', strtotime($elem['ACTIVE_FROM']));
        // var_dump($elem);die();
    }
    // var_dump($result);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    // while($ob = $res->GetNextElement()){
    //     $arFields = $ob->GetFields();
    //     // print_r($arFields);
    //     // $arProps = $ob->GetProperties();
    //     // print_r($arProps);
    // }
}
?>
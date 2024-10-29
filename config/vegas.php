<?php
//use Illuminate\Support\Arr;


// системные настройки
$systemConfig = [

    'general_plan_start' => '01.08.2023',
    'general_plan_end'   => '31.08.2023',

];


// настройки обновления
$updateConfig = [
    'data_1C_folder'                                 => 'data1C',
    'sellers_1C_json_name'                           => 'sellers(SRM)',
    'sellers_materials_1C_json_name'                 => 'materials(SRM)',
    'seller_seller_material_relationships_json_name' => 'seller_seller_material_relationships(SRM)',
    'models_1C_json_name'                            => 'models',
    'collections_1C_json_name'                       => 'collections',
    'groups_1C_json_name'                            => 'groups',
    'categories_1C_json_name'                        => 'categories',
    'materials_1C_json_name'                         => 'materials',
    'procedures_1C_json_name'                        => 'procedures',
    'clients_EPS_json_name'                          => 'clients',

    'orders_EPS_json_name'                           => 'orders',
    'parts_EPS_json_name'                            => 'parts',
    'lines_EPS_json_name'                            => 'lines',

];

// группы материалов, которые нужно заносить в таблицу с группами
$groupsActiveList = [

    '000001933' => 'С_AIRFOAM',
    '000000003' => 'С_Кокос',
    '000000011' => 'С_Латекс',
    '000000021' => 'С_ППУ',
    '000000028' => 'С_Блок пружинный',
    '000000447' => 'С_Тонкий настил',
    '000000448' => 'С_Клей',
    '000021450' => 'С_Подушки',
    '000021510' => 'С_Наполнители разные',
    '000000451' => 'У_Ящики',
    '000001099' => 'У_Маркировка',
    '000001151' => 'У_Разное',
    '000033782' => 'У_Пленка и Мешки',
    '000034310' => 'У_Сумки комбинированные',
    '000000450' => 'Ш_Тесьмы и канты',
    '000000829' => 'Ш_Маркировка чехла',
    '000001368' => 'Ш_Слайдеры и Аэраторы',
    '000001378' => 'Ш_Молния рулонная',
    '000001386' => 'Ш_Молния разъемная',
    '000001435' => 'Ш_Боковины чехла',
    '000001453' => 'Ш_Ручки для матраса',
    '000001782' => 'Ш_Ткани',
    '000001790' => 'Ш_Нитки',
    '000001791' => 'Ш_Наполнители стеганного полотна',
    '000001854' => 'Ш_Спанбонд',
    '000021515' => 'Ш_Молния неразъемная',
    '000001441' => '3D Сетка',
    '000002145'	=> '4_Под ЗАКАЗ и ТЕНДЕР',
    '000000062' => 'ПФ_Полотна стеганные'


];

// поля таблицы sellers
$sellersTableFields = [
    'code1C'     => 'code1C',
    'full_name'  => 'full_name',
    'short_name' => 'short_name',
    'country'    => 'country',
    'address'    => 'address',
    'longitude'  => 'longitude',
    'latitude'   => 'latitude',
];

return array_merge(
    $updateConfig,
    $systemConfig,
    $sellersTableFields,
    $groupsActiveList,
    []
);

/*

через helper Laravel
return $array = Arr::collapse([
    $systemConfig,
    []
]);
*/










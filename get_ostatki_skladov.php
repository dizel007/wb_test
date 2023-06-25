<?php
require_once "functions/topen.php";
require_once "functions/functions.php";
require_once "wb_catalog.php"; // массиво с каталогов наших товаров
echo '<link rel="stylesheet" href="css/main_table.css">';
$arr_catalog = get_catalog_wb ();

foreach ($arr_catalog as $items) {
    $arr_skus[] = $items['barcode'];
}

$warehouseId = 34790;// ID склада ООО на ВБ

$link_wb  = "https://suppliers-api.wildberries.ru/api/v3/stocks/".$warehouseId;

$data = array("skus"=> $arr_skus);

//    echo "<pre>";
//    print_r($data);
    
 
$ch = curl_init($link_wb);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization:' . $token_wb,
	'Content-Type:application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);

$res = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Получаем HTTP-код
curl_close($ch);

echo     'Результат обмена : '.$http_code. "<br>";

$res = json_decode($res, true);


// echo "<pre>";
// print_r($res);


foreach ($res['stocks'] as $prods)  {
    foreach ($arr_catalog as &$items) {
        if ($prods['sku'] == $items['barcode']) {
$items['quntity'] = $prods['amount'];
        }

}
}

/*******************************************************************************************************
* *****************************      Достаем фактически заказанные товары
*******************************************************************************************************/


$ch = curl_init('https://suppliers-api.wildberries.ru/api/v3/orders/new');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization:' . $token_wb,
	'Content-Type:application/json'
));
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);

$res = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Получаем HTTP-код
curl_close($ch);

echo     'Результат обмена : '.$http_code. "<br>";

$result = json_decode($res, true);




// формируем массив ключ - артикул ; значение - количество элементов этого артикула

foreach ($result['orders'] as $itemss) {
 
	$arr_name[$itemss['article']][]= $itemss;
$sum = @$sum + $itemss['convertedPrice']/100;

}

// echo "<pre>";
// print_r($arr_catalog);
// die('ddnnnn333nn');


foreach ($arr_name as $key => $temp_items) {
	$arr_article_count[$key] = count($arr_name[$key]);
}

// echo "<pre>";
// print_r($arr_catalog);
// die('dddd');

foreach ($arr_article_count as $key=>$prods)  {
    foreach ($arr_catalog as &$items) {
        // echo "<br>key=$key<br>";
        if ($key == $items['article']) {

            $items['sell_count'] = $prods;
            
        } 
    }

}


// echo "<pre>";
// print_r($arr_catalog);



echo <<<HTML
<table>
<tr class="prods_table">
    <td>артикул</td>
    <td>Наименование</td>
    <td>БарКод</td>
    <td>Кол-во на складе<br>(Остаток)</td>
    <td>Кол-во продано</td>
    <td>Обновить остатки из 1С</td>

</tr>
HTML;
foreach ($arr_catalog as $items) {
    $article = $items['real_article'];
    $name = $items['name'];
    $quntity = $items['quntity'];
    $barCode =  $items['barcode'];
    isset($items['sell_count'])?$sell_count = $items['sell_count']:$sell_count = 0;

echo <<<HTML
<tr class="prods_table">
    <td>$article</td>
    <td>$name</td>
    <td>$barCode</td>

    <td>$quntity</td>
    <td>$sell_count</td>
    <td><input type="number" value=0></td>

</tr>

HTML;
}
echo "</table>";






<?php
echo "WB IP";
$test = array("limit" =>"1000",
"next" => 0);

$js_test = json_encode($test );


echo $js_test;


$token_wb = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6IjM1ZWQ5YWIyLWY4NzAtNGFkYi1hN2IwLTA0ZTUzN2NkZjdmZCJ9.gzboXCOqiAd7n6ovPCTjyTngEJtQYzMuAEx2Gu0QGXw';
$token_wb_ip = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6IjVkNTdjMjlkLTg2OWItNDlhMS04Y2UxLWQxZmZkZTVkNWJjOCJ9.wsMvlWA3Rc3v2PQAamV_cg5MEuBgIH18LmyBP37GCGM';
$data = array(
	'limit' => 1000,
    "next" => 0,
    
	);
 
$ch = curl_init('https://suppliers-api.wildberries.ru/api/v3/orders/new');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization:' . $token_wb_ip,
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

$res = json_decode($res, true);


foreach ($res['orders'] as $items) {

	$arr_name[$items['article']][] = $items;
$sum = @$sum + $items['convertedPrice']/100;

}


foreach ($arr_name as $key=>$items) {
	$bbbb[$key] = count($arr_name[$key]);
}
echo "<pre>";
print_r($bbbb);

echo count($res['orders']);
echo "<br>";
echo number_format($sum,2);





die();

echo <<<HTML
<table>
<tr>
	<td>пп</td>
	<td>article</td>
	<td>nmId</td>
	<td>convertedPrice</td>

</tr>
HTML;
$i=1;
foreach ($res['orders'] as $items) {
	$article =  $items['article'];
	$nmId =  $items['nmId'];
	$convertedPrice =  $items['convertedPrice'];
	$article =  $items['article'];

	echo "<tr>
	<td>$i</td>
	<td>$article</td>
	<td>$nmId</td>
	<td>$convertedPrice</td>

</tr>";
$i++;
}


echo "</table>";
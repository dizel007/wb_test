<?php


$token_wb = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6IjM1ZWQ5YWIyLWY4NzAtNGFkYi1hN2IwLTA0ZTUzN2NkZjdmZCJ9.gzboXCOqiAd7n6ovPCTjyTngEJtQYzMuAEx2Gu0QGXw';
$data = array(
	'name' => "Тест АПИ"
    
	);
 
$ch = curl_init('https://suppliers-api.wildberries.ru/api/v3/supplies');
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


echo "<pre>";
print_r($res);

die('sssssss');

<?php
require_once "functions/topen.php";
require_once "functions/functions.php";
$rekl_wb_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6ImIzNDk3YzRjLTBhMmYtNDViMC04YWM4LTQxODA5MzcxNTY5MiJ9.ar7jXzp4G-Yy94IBU-xFCMuE6mOSJGnttqWBKTR77P0";
$wb_link = "https://advert-api.wb.ru/adv/v0/allcpm";
$dop_link = "?type=6";
$new_wb_link = $wb_link.$dop_link;


echo $new_wb_link."<br>";
$data = array('param' => "6926528");

$res = light_query_with_data($rekl_wb_token, $new_wb_link, $data);

echo "<br>";
print_r($res);
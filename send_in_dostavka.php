<?php
require_once "functions/topen.php";
require_once "functions/functions.php";

$link_wb = "https://suppliers-api.wildberries.ru/api/v3/supplies?limit=1000&next=0";

$new_postavki = light_query_without_data($token_wb, $link_wb);

$supplyId = "WB-GI-49941076";
// передаем поставку в доставку
$link_wb = "https://suppliers-api.wildberries.ru/api/v3/supplies/".$supplyId."/deliver";


$data = array('supplyId' => $supplyId);
$res_patch = patch_query_with_data($token_wb, $link_wb, $data);


echo "<pre>";
print_r($res_patch);

die('<br>eeee');

<?php

$data = file_get_contents('12.json');
$arr_data = json_decode($data,true);

echo "<pre>";
print_r($arr_data);


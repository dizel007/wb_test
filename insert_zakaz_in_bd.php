<?php

function insert_zakaz_in_db($pdo, $array_item, $supplyId) {
$date_change = date('Y-m-d');
echo "<br>$supplyId<br>";

$id= $array_item['id'];
$created_date= $array_item['createdAt'];
$skus= $array_item['skus'][0];
$convertedPrice= $array_item['convertedPrice'];
$status_task= 'confirm';
$status_supply= 'confirm';
// Проверяем если ли какой нибуль комментарий, если нет, то не добавляем строку
// (isset($dop_comment))?$dop_comment = $dop_comment: $dop_comment='';

$stmt  = $pdo->prepare("INSERT INTO `tasks` 
                      (id, 	created_date, skus, convertedPrice, supplyId, status_task, status_supply, processing_date)
                       VALUES (:id, :created_date, :skus, :convertedPrice, :supplyId, :status_task, :status_supply, :processing_date)");

$stmt ->bindParam(':id', $id);
$stmt ->bindParam(':created_date', $created_date);
$stmt ->bindParam(':skus', $skus);
$stmt ->bindParam(':convertedPrice', $convertedPrice);
$stmt ->bindParam(':supplyId', $supplyId);
$stmt ->bindParam(':status_task', $status_task);
$stmt ->bindParam(':status_supply', $status_supply);
$stmt ->bindParam(':processing_date', $date_change);


if ($stmt ->execute()) {
  $last_id = $pdo->lastInsertId(); // получаем id - введенной строки 
  echo "Запись УДАЧНО добавлена successfully";
} else {
  die ("Какой то облом, со вставкой записи в таблицу reports");
}
}
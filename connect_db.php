<?php


// Подюключение к БД
$host="localhost";//имя  сервера
$user="root";//имя пользователя
$password="";//пароль
$db="wb_ooo";//имя  базы данных

$reestrKP = "tasks"; // 

// ************************************** PDO ***********************************

 
      try {  
        $pdo = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $password);
        $pdo->exec('SET NAMES utf8');
        } catch (PDOException $e) {
          print "Has errors: " . $e->getMessage();  die();
        }

// *************   проверяем зашел ли пользователь с паролем  ****************************************


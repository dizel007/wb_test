<?php
require_once "functions/topen.php";
require_once "functions/functions.php";

$Zakaz_v_1c = "2222";
$arr_supply['82401-К'] =  
array('supplayId'      =>  "WB-GI-50277515",
      'name_postavka'  =>  "1268-(82401-К) 17шт");


$file_json = "json_supply/".$Zakaz_v_1c." от ".date("Y-M-d").".json";
$filedata_json = json_encode($arr_supply);
// file_put_contents($file_json, $filedata_json, FILE_APPEND); // добавляем данные в файл с накопительным итогом




die();
/**
 * 
 */
$data = file_get_contents($file_json);
$arr_data = json_decode($data,true);

echo "<pre>";
print_r($arr_data); 

die();
foreach ($arr_data as $key=>$supply) {

            echo "<br> Номер поставки:".$supply['supplayId']."<br>";  
put_supply_in_deliver ($token_wb, $supply['supplayId']);

            echo "<br>***** Дошли до траты времени ******<br>";
        usleep(500000); // 
            echo "<br>***** Вышли после траты времени *****<br>";
$app_qr_pdf_file_names[] = get_qr_cod_supply($token_wb, $supply['supplayId'], $supply['name_postavka'] );
}

echo "<pre>";
print_r($app_qr_pdf_file_names);




 function put_supply_in_deliver ($token_wb, $supplyId){
        $link_wb = "https://suppliers-api.wildberries.ru/api/v3/supplies/".$supplyId."/deliver";
        echo "<br>$link_wb<br>";
    //  Запуск добавления товара в поставку - НЕВОЗВРАТНАЯ ОПЕРАЦИЯ ***********************************
    // раскоментировать при работе
        $res =  patch_query_with_data($token_wb, $link_wb, "");
        echo "<pre>";
        print_r($res);
        return $res;
}


function get_qr_cod_supply($token_wb, $supplyId, $name_postavka){
$dop_link="?type=png";  // QUERY PARAMETERS
$link_wb  = "https://suppliers-api.wildberries.ru/api/v3/supplies/".$supplyId."/barcode".$dop_link;

$qr_supply = light_query_without_data($token_wb, $link_wb); // запрос QR кода поставки
require_once "libs/fpdf/fpdf.php";
//create pdf object
$pdf = new FPDF('L','mm', array(151, 107));
//add new page
$pdf->AliasNbPages();
$filedata=''; // очищаем строку для ввода данных
$pdf->AddPage();

$file = "EXCEL/".$supplyId.".png"; // название пнг
$filedata = base64_decode($qr_supply['file']);
    file_put_contents($file, $filedata, FILE_APPEND);
$pdf->image($file,0,0,'PNG');
unlink ($file); // удаляем png файл

$pdf_file = $name_postavka.".pdf"; // название PDF  которое сохраниться в итоге
$pdf->Output("EXCEL/".$pdf_file, 'F');

return $pdf_file;
}


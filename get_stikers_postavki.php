<?php
require_once "functions/topen.php";
require_once "functions/functions.php";



$arr_orders = array( 872883601, 872828421, 873053235, 873130653);
get_stiker_from_supply ($token_wb, $arr_orders, "2154","402-x");


function get_stiker_from_supply ($token_wb, $arr_orders, $N_1C_zakaz, $article) {
$dop_link="?type=png&width=40&height=30";  // QUERY PARAMETERS

$link_wb  = "https://suppliers-api.wildberries.ru/api/v3/orders/stickers".$dop_link;

$data = array(

    "orders"=> $arr_orders

);
    
$res_stikers = light_query_with_data($token_wb, $link_wb, $data); 

echo "<pre>";
print_r($res_stikers);



require_once "libs/fpdf/fpdf.php";
//create pdf object
$pdf = new FPDF('L','mm', array(80, 106));
//add new page
$pdf->AliasNbPages();


foreach ($res_stikers['stickers'] as $items) {
$filedata='';
$pdf->AddPage();
$file = "EXCEL/stik.png";
$filedata = base64_decode($items['file']);

file_put_contents($file, $filedata, FILE_APPEND);

$pdf->image($file,0,0,'PNG');
unlink ($file);



}
$pdf_file = "№".$N_1C_zakaz."_stikers_(".$article.") ".count($res_stikers['stickers'])."шт.pdf";
$pdf->Output("EXCEL/".$pdf_file, 'F');
}
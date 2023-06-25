<?php
require_once "functions/topen.php";
require_once "functions/functions.php";

require_once 'libs/PHPExcel-1.8/Classes/PHPExcel.php';
require_once 'libs/PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php';
require_once 'libs/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';

$Zakaz_v_1c = 5555;
$arr_orders = get_orders_from_supply($token_wb, "WB-GI-50180120");
usleep(10000)  ;
foreach ($arr_orders as $orders) {
    $new_real_arr_orders[] = $orders['id'];
}
// get_stiker_from_supply ($token_wb, $new_real_arr_orders, "2154", "82402-з");

echo "<pre>";
print_r($arr_orders);

$i=1;
$sum=0;
foreach ($arr_orders as $item) {
$sum = $sum + $item['convertedPrice'];
}
$midlle_price=number_format(($sum/count($arr_orders))/100,2);
// формируем ексель файлик для 1С 
$xls = new PHPExcel();
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();
        
       $new_key = make_right_articl($arr_orders[0]['article']);
       $sheet->setCellValue("A".$i, $new_key);
       $sheet->setCellValue("C".$i, count($arr_orders));
       $sheet->setCellValue("D".$i, $midlle_price); // средняя цена за 1 шт товара

             $i++; // смешение по строкам
        

        
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $rnd100000 = "(".rand(0,100000).")";
       
        $file_name_1c_list = $Zakaz_v_1c."_".date('Y-m-d').$rnd100000."_file_1C.xlsx";
        $objWriter->save("EXCEL/".$file_name_1c_list);
        $link_list_tovarov = "EXCEL/".$file_name_1c_list;
// формируем ексель файлик для 1С 
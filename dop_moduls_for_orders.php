<?php
echo "START ___DOP_MODULS_FOR_ORDERs";



/*************************************************************
 *  *****   Формируем ексель файл для 1С
 ***************************************************************/
function make_1c_file_($arr_for_1C_file, $new_arr_new_zakaz, $Zakaz_v_1c) {
// формируем ексель файлик для 1С 
$xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        $i=1;

foreach ($arr_for_1C_file  as $key => $items) {
           $right_article = make_right_articl($key);
            $sheet->setCellValue("A".$i, $right_article);
            $sheet->setCellValue("C".$i, count($new_arr_new_zakaz[$key]));
            // высчитываем среднюю цену за товар
            $sum=0;
            foreach ($items as $item) {
                $sum = $sum + $item['convertedPrice'];
                }
            $midlle_price=number_format(($sum/count($items))/100,2);
            $sheet->setCellValue("D".$i, $midlle_price); // цена за 1 шт товара
            $i++; // смешение по строкам
        
       }
        
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $rnd100000 = "(".rand(0,100000).")";
        $file_name_1c_list = $Zakaz_v_1c."_".date('Y-m-d').$rnd100000."_file_1C(DOP).xlsx";
        $objWriter->save("EXCEL/".$file_name_1c_list);
}



/*************************************************************
 *  *****   Формируем архив со стикерами для данного Заказа
 ***************************************************************/
function make_zip_archive($ArrFileNameForZIP, $Zakaz_v_1c, $file_name_1c_list ) {
$zip = new ZipArchive();
$zip->open("zip_arc/".$Zakaz_v_1c." от ".date("Y-M-d").".zip", ZipArchive::CREATE|ZipArchive::OVERWRITE);
 foreach ($ArrFileNameForZIP as $zips) {
    $zip->addFile("EXCEL/".$zips, "$zips"); // Добавляем пдф файлы
 }
    $zip->addFile("EXCEL/"."(DOP)".$file_name_1c_list, "$file_name_1c_list"); // добавляем для 1С файл
    $zip->close();   

}

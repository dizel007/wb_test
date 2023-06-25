<?php
require_once "connect_db.php";
require_once "functions/topen.php";
require_once "functions/functions.php";
require_once "insert_zakaz_in_bd.php";
require_once "dop_moduls_for_orders.php";


require_once 'libs/PHPExcel-1.8/Classes/PHPExcel.php';
require_once 'libs/PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php';
require_once 'libs/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';



$Zakaz_v_1c = "7777";
// Получаем все новые заказы с сайта ВБ
$arr_new_zakaz = get_all_new_zakaz ($token_wb);

// Сформировали массив с ключем - артикулом и значением - массив отправлений
foreach ($arr_new_zakaz['orders'] as $items) {
    $new_arr_new_zakaz[$items['article']][] = $items;
}

/******************************************************************************************
 *  ************   Начинаем главный разбор ассоциативного массива
 ******************************************************************************************/

foreach ($new_arr_new_zakaz  as $key => $items) {
    
    echo $key."<br>";
    $right_article = make_right_articl($key);
    $name_postavka = $Zakaz_v_1c."-(".$right_article.") ".count($new_arr_new_zakaz[$key])."шт";
    // формируем одну поставку и туда суем весь товар с этим артикулом
    $supplyId = make_postavka ($token_wb, $name_postavka); // номер поставки
    $arr_supply[$right_article] =  array('supplayId'      =>  $supplyId['id'],
                                         'name_postavka'  =>  $name_postavka);
        foreach ($items as $item) {
           $orderId = $item['id']; // номер заказа для добавления в сборку
        
    // Добавляем строку в БД 
    // insert_zakaz_in_db($pdo, $item, $supplyId['id']);
    
    //  Запуск добавления товара в поставку - НЕВОЗВРАТНАЯ ОПЕРАЦИЯ ***********************************
    //****  раскоментировать при работе
    //****  раскоментировать при работе
    //  $res_query[] = make_sborku_one_article_one_zakaz ($token_wb, $supplyId['id'], $orderId);
   
        }
usleep(100000);
    $arr_real_orders = get_orders_from_supply($token_wb, $supplyId['id']); // список Заказов которые ТОЧНО полпали в Поставку
 
    // ***************************  Формируем массив реально попавших в поставку заказов
 		foreach ($arr_real_orders as $orders) {
			$new_real_arr_orders[] = $orders['id'];
		}
usleep(100000);

// *********************  формируем и сохраняем стикеры себе на комп
if (isset($new_real_arr_orders)) { // проверят есть ли массив 
    $ArrFileNameForZIP[] = get_stiker_from_supply ($token_wb, $new_real_arr_orders, $Zakaz_v_1c , $right_article); // формируем стикеры за этой поставки
} else {
    echo ("НЕТ данных для формирования этикеток. Возможно заказы не подгрузили в поставку WB№_".$supplyId['id']." .<br>");
   }
// *********************  формируем массив реальных заказов для 1С ******
   $arr_for_1C_file[$key] = $arr_real_orders; // 
//*********** aформируем json со списком отправлений  ****************
// save_stikers_json ($new_real_arr_orders, $Zakaz_v_1c, $key);

// ***** Массив с реальными заказами из поставки
$MEGA_arr_real_orders[$key]=$arr_real_orders;


//*********** удаляем временные массивы ****************
unset($arr_real_orders);
unset($new_real_arr_orders);


}


/******************************************************************************************
 *  ************   Формируем ексель файл для 1С
 ******************************************************************************************/

echo "**************** Формируем ексель файлик для 1С  ******************************************<br>";

$xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        $i=1;

echo "**************** МАССИВ ПО Которому формируем 1С файл *********<br>";
echo "<pre>";
print_r($arr_for_1C_file);

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
       
        $file_name_1c_list = $Zakaz_v_1c."_".date('Y-m-d').$rnd100000."_file_1C.xlsx";
        $objWriter->save("EXCEL/".$file_name_1c_list);
   
/******************************************************************************************
 *  ***************   Формируем архив со стикерами для данного Заказа
 ******************************************************************************************/
$zip = new ZipArchive();
$zip->open("zip_arc/".$Zakaz_v_1c." от ".date("Y-M-d").".zip", ZipArchive::CREATE|ZipArchive::OVERWRITE);
 foreach ($ArrFileNameForZIP as $zips) {
    $zip->addFile("EXCEL/".$zips, "$zips"); // Добавляем пдф файлы
 }
    $zip->addFile("EXCEL/".$file_name_1c_list, "$file_name_1c_list"); // добавляем для 1С файл
    $zip->close();   

/******************************************************************************************
 *  ************************   Формируем JSON со списком поставок
 ******************************************************************************************/
 
$file_json = "json_supply/".$Zakaz_v_1c." от ".date("Y-M-d").".json";
$filedata_json = json_encode($arr_supply, JSON_UNESCAPED_UNICODE);
file_put_contents($file_json, $filedata_json, FILE_APPEND); // добавляем данные в файл с накопительным итогом

/******************************************************************************************
 *  *********************   Формируем JSON со списком реальных заказов (ДЛЯ ОТРАБОТКИ)
 ******************************************************************************************/
 
 $file_json = "json_supply/".$Zakaz_v_1c." от ".date("Y-M-d")."_real_orders.json";
 $filedata_json = json_encode($MEGA_arr_real_orders, JSON_UNESCAPED_UNICODE);
 file_put_contents($file_json, $filedata_json); // добавляем данные в файл с накопительным итогом


/******************************************************************************************
 *  **************   ДУБЛИРУЕМ ВСЕ ОПЕРАЦИИ С ПОМОЩТЮ ФУНКЦИЙ (ДЛЯ ОТРАБОТКИ)
 ******************************************************************************************/
make_1c_file_($arr_for_1C_file, $new_arr_new_zakaz, $Zakaz_v_1c);  // ексель для 1С
make_zip_archive($ArrFileNameForZIP, $Zakaz_v_1c, $file_name_1c_list ); // zip архив этикеток и 1с файла


die('FINISH WORK');

// Функция готовить информацию и запускает добавление товара в поставку
function make_sborku_one_article_one_zakaz ($token_wb, $supplyId, $orderId){
    $data = array(
        'supplyId' => $supplyId,
        'orderId' => $orderId
        );
        $link_wb = 'https://suppliers-api.wildberries.ru/api/v3/supplies/'.$supplyId."/orders/".$orderId;
        echo "<br>$link_wb<br>";
    //  Запуск добавления товара в поставку - НЕВОЗВРАТНАЯ ОПЕРАЦИЯ ***********************************
    // раскоментировать при работе
        $res =  patch_query_with_data($token_wb, $link_wb, $data);
        echo "<pre>";
        print_r($res);
        return $res;
}


function save_stikers_json ($arr_orders, $Zakaz_v_1c, $article){
    $file_json = "json_supply/".$Zakaz_v_1c." от ".date("Y-M-d")."-(".$article.").json";
    $filedata_json = json_encode($arr_orders);
    file_put_contents($file_json, $filedata_json, FILE_APPEND); // добавляем данные в файл с накопительным итогом

}
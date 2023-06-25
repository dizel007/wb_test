<?php

$zip = new ZipArchive();
$zip->open('zip_arc/archive.zip', ZipArchive::CREATE|ZipArchive::OVERWRITE);
$zip->addFile('EXCEL/№№1248_stikers_(1840-30) 5шт.pdf', '№№1248_stikers_(1840-30) 5шт.pdf');
$zip->addFile('EXCEL/№№1248_stikers_(1940-10) 5шт.pdf', '№№1248_stikers_(1940-10) 5шт.pdf');
$zip->addFile('EXCEL/№№1248_stikers_(7262-КП) 6шт.pdf', '№№1248_stikers_(7262-ssКП) 6шт.pdf');
$zip->close();



<?php
/**
 * example for import
 */

// IO物件建構
$io = new \nueip\io\IO();
// 匯入處理 - 取得匯入資料
$data = $io->import($builder = 'Excel', $fileArgu = 'fileupload');

$configName = $io->getConfig()->getOption('configName');

echo 'Config Name = ' . $configName . "<br>\n";
echo 'Data = ';
var_export($data);
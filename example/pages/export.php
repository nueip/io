<?php
// 取得原始資料
$data = [
    [
        'u_no' => 'A001',
        'c_name' => 'Mars',
        'id_no' => 'A234567890',
        'birthday' => '2000-01-01',
        'gender' => '1',
    ],
    [
        'u_no' => 'A001',
        'c_name' => 'Jack',
        'id_no' => 'A123456789',
        'birthday' => '20001-01-01',
        'gender' => '1',
    ]
];

// IO物件建構
$io = new \marshung\io\IO();

// 匯出處理 - 建構匯出資料 - 簡易模式結構定義物件-範本
$io->export($data, $config = 'SimpleExample', $builder = 'Excel', $style = 'Io');
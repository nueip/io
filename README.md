匯出匯入模組
===

# 說明
為簡化匯出匯入使用法式，編寫此模組

# 用法
```
$io = new \app\libraries\io\IO();
$io->export($data, $config = 'AddIns', $builder = 'Excel', $style = 'Nueip');

// === 匯入 ===
// IO物件建構
$io = new \app\libraries\io\NueipIO();
// 匯入處理 - 取得匯入資料
$data = $io->import($config = 'AddIns', $builder = 'Excel');
```
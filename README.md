匯出匯入模組
===

# 說明
為簡化匯出匯入使用法式，編寫此模組

# 用法
## 有資料的結構定義物件
### 匯出
```
// === 匯出 ===
// 取得原始資料
$data = $data;

// IO物件建構
$io = new \marshung\io\IO();
// 匯出處理 - 建構匯出資料 - 有資料的結構定義物件
$io->export($data, $config = 'AddIns', $builder = 'Excel', $style = 'Io');
```

### 匯入
```
// === 匯入 ===
// IO物件建構
$io = new \marshung\io\IO();
// 匯入處理 - 取得匯入資料 - 有資料的結構定義物件
$data = $io->import($config = 'AddIns', $builder = 'Excel');
```

## 空的結構定義物件
### 匯出
```
// === 匯出 ===
// 取得原始資料
$data = $data;

// IO物件建構
$io = new \marshung\io\IO();
// 匯出處理 - 建構匯出資料 - 空的結構定義物件
$io->export($data, $config = 'Empty', $builder = 'Excel', $style = 'Io');
```

### 匯入
```
// === 匯入 ===
// IO物件建構
$io = new \marshung\io\IO();
// 匯入處理 - 取得匯入資料 - 有資料的結構定義物件
$data = $io->import($config = 'Empty', $builder = 'Excel');
```

## 手動處理 - 外部處理
### 匯出
```
// === 匯出 ===
// 取得原始資料
$data = $data;

// 結構定義-簡易模式
$defined = array(
    'u_no' => '員工編號',
    'c_name' => '姓名',
    'id_no' => '身分證字號',
    'birthday' => '出生年月日',
    'gender' => '性別'
);

// IO物件建構
$io = new \marshung\io\IO();

// 手動建構相關物件
$io->setConfig()
    ->setBuilder()
    ->setStyle();

// 載入外部定義
$conf = $io->getConfig()
    ->setTitle($defined)
    ->setContent($defined);

// 建構外部對映表
$listMap = array(
    'gender' => array(
        array(
            'value' => '1',
            'text' => '男'
        ),
        array(
            'value' => '0',
            'text' => '女'
        )
    )
);

// 載入外部對映表
$conf->setList($listMap);

// 匯出處理 - 建構匯出資料
$io->setData($data)->exportBuilder();
```

### 匯入
```
// === 匯入 ===
// 結構定義-簡易模式
$defined = array(
    'u_no' => '員工編號',
    'c_name' => '姓名',
    'id_no' => '身分證字號',
    'birthday' => '出生年月日',
    'gender' => '性別'
);

// === 匯入 ===
// IO物件建構
$io = new \marshung\io\IO();

// 手動建構相關物件
$io->setConfig()
    ->setBuilder()
    ->setStyle();

// 載入外部定義
$conf = $io->getConfig()
    ->setTitle($defined)
    ->setContent($defined);

// 建構外部對映表
$listMap = array(
    'gender' => array(
        array(
            'value' => '1',
            'text' => '男'
        ),
        array(
            'value' => '0',
            'text' => '女'
        )
    )
);

// 載入外部對映表
$conf->setList($listMap);

// 取得Builder
$builder = $io->getBuilder();

// 匯入處理 - 取得匯入資料
$data = $io->import($conf, $builder);
```






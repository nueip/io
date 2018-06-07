匯出匯入模組
===

# 說明
為簡化匯出匯入使用法式，編寫此模組

# 用法
## 有資料的結構定義物件
```
// === 匯出 ===
// IO物件建構
$io = new \app\libraries\io\IO();
// 匯出處理 - 建構匯出資料 - 有資料的結構定義物件
$io->export($data, $config = 'AddIns', $builder = 'Excel', $style = 'Nueip');

// === 匯入 ===
// IO物件建構
$io = new \app\libraries\io\NueipIO();
// 匯入處理 - 取得匯入資料 - 有資料的結構定義物件
$data = $io->import($config = 'AddIns', $builder = 'Excel');
```

## 空的結構定義物件
```
// === 匯出 ===
// IO物件建構
$io = new \app\libraries\io\IO();
// 匯出處理 - 建構匯出資料 - 空的結構定義物件
$io->export($data, $config = 'Empty', $builder = 'Excel', $style = 'Nueip');

// === 匯入 - 未檢查 ===
// IO物件建構
$io = new \app\libraries\io\NueipIO();
// 匯入處理 - 取得匯入資料 - 空的結構定義物件
$data = $io->import($config = 'Empty', $builder = 'Excel');
```

## 手動處理
```
// === 匯出 ===
// IO物件建構
$io = new \marshung\io\IO();

// 手動建構相關物件
$io->setConfig()
    ->setBuilder()
    ->setStyle();
// 載入外部定義
$io->getConfig()
    ->setTitle($defined)
    ->setContent($defined);


// 載入外部對映表
$iConf = new \marshung\io\config\AddInsConfig();
$listMap = $iConf->getList();

$conf = $io->getConfig();
$conf->setList('u_country', $listMap['u_country']);
$conf->setList('iu_sn', $listMap['iu_sn']);
$conf->setList('rate_company', $listMap['rate_company']);
$conf->setList('ins_status', $listMap['ins_status']);
$conf->setList('disability_level', $listMap['disability_level']);
$conf->setList('assured_category', $listMap['assured_category']);
$conf->setList('labor_insurance_1', $listMap['labor_insurance_1']);
$conf->setList('labor_insurance_2', $listMap['labor_insurance_2']);
$conf->setList('emp_insurance', $listMap['emp_insurance']);
$conf->setList('labor_retir_system', $listMap['labor_retir_system']);
$conf->setList('ins_category', $listMap['ins_category']);
$conf->setList('subsidy_eligibility', $listMap['subsidy_eligibility']);
$conf->setList('laborLevel', $listMap['laborLevel']);
$conf->setList('labor_salary', $listMap['labor_salary']);
$conf->setList('pensionLevel', $listMap['pensionLevel']);
$conf->setList('labor_retir_salary', $listMap['labor_retir_salary']);
$conf->setList('nhiLevel', $listMap['nhiLevel']);
$conf->setList('heal_ins_salary', $listMap['heal_ins_salary']);

// 匯出處理 - 建構匯出資料
$io->setData($data)->exportBuilder();

// === 匯入 - 未實作 ===
```
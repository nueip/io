<?php
namespace marshung\io\builder;

/**
 * 格式處理總成物件
 *
 * 規則：
 * 1. 第一張表一定是參數工作表，名稱為ConfigSheet，並隱藏、鎖定
 * 2. 參數工作表負責記錄excel各式參數，以供讀取分析用
 * 3. 標題、結尾為一筆定義一列
 * 4. 內容為一筆定義多列
 *
 * 樣式設定提要：
 * 1. 欄位預設格式為文字，如需要對欄位格式變更，可在結構設定中指定特殊樣式
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class ExcelBuilder
{

    /**
     * 預設參數
     *
     * @var array
     */
    protected $_options = array(
        'fileName' => 'export',
        'builder' => 'excel',
        'builderVersion' => '0.1'
    );

    /**
     * 資料
     *
     * @var array
     */
    protected $_data = array();

    /**
     * 定義資料
     *
     * @var object
     */
    protected $_config = null;

    /**
     * Style定義資料
     *
     * @var object
     */
    protected $_style = null;

    /**
     * 下拉選單定義資料
     *
     * @var array $_listMap['目標鍵名'] = array(array('value' => '數值','text' => '數值名稱'),.....);
     */
    protected $_listMap = array();

    /**
     * phpSpreadsheetHelper
     *
     * @var object \yidas\phpSpreadsheet\Helper
     */
    protected $_builder = null;

    /**
     * 欄位座標參數表
     *
     * @var array
     */
    protected $_offsetMap = array();

    /**
     * 下拉選單結構位址
     *
     * 從下拉選單定義建構在ConfigSheet中的位址資料
     *
     * @var array
     */
    protected $_listAddrMap = array();

    /**
     * Construct
     *
     * @param object $phpSpreadsheet
     *            Excel物件/檔案路徑
     * @throws Exception
     */
    public function __construct($phpSpreadsheet = NULL)
    {
        // 初始化
        $this->init($phpSpreadsheet);
    }

    /**
     * Destruct
     */
    public function __destruct()
    {}

    /**
     * *********************************************
     * ************** Public Function **************
     * *********************************************
     */
    
    /**
     * 初始化
     *
     * @param object $phpSpreadsheet
     *            Excel物件/檔案路徑
     */
    public function init($phpSpreadsheet = NULL)
    {
        // 初始化
        if (is_null(\yidas\phpSpreadsheet\Helper::getSpreadsheet()) || ! is_null($phpSpreadsheet)) {
            // 未初始化過、有傳入初始化目標 - 執行初始化
            $this->_builder = \yidas\phpSpreadsheet\Helper::newSpreadsheet($phpSpreadsheet);
        } else {
            // 已初始化過 - 只取物件alias
            $this->_builder = "\yidas\phpSpreadsheet\Helper";
        }
        
        if (! $phpSpreadsheet) {
            // 新建Excel時才設定
            $this->_builder->getSpreadsheet()
                ->getProperties()
                ->setTitle("Office 2007 XLSX Document");
        }
        
        return $this;
    }

    /**
     * 載入檔案
     *
     * 此函式為匯入時載入檔案，需檢查傳入資料副檔名、格式，並使用phpSpreadsheet只讀資料模式
     *
     * @param string $upFilePath
     *            檔案路徑
     */
    public function loadFile($upFilePath, $upFileName)
    {
        // 檔案資料
        $fileFullName = basename($upFileName);
        // 取得檔案名稱、副檔名
        $fileName = substr($fileFullName, 0, strrpos($fileFullName, '.'));
        $fileExt = strtolower(substr($fileFullName, strrpos($fileFullName, '.') + 1, strlen($fileFullName) - strlen($fileName) + 1));
        
        // 檔案檢查 - 副檔名
        if (! in_array($fileExt, array(
            'xlsx',
            'xls'
        ))) {
            throw new \Exception(get_language('wrongtype'), 400);
        }
        
        // 使用資料讀取模式取得$spreadsheet物件
        /**
         * Identify the type of $inputFileName *
         */
        $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($upFilePath);
        /**
         * Create a new Reader of the type that has been identified *
         */
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
        /**
         * Advise the Reader that we only want to load cell data *
         */
        $reader->setReadDataOnly(true);
        /**
         * Load $inputFileName to a Spreadsheet Object *
         */
        $spreadsheet = $reader->load($upFilePath);
        
        // 將$spreadsheet物件載入Helper
        $this->init($spreadsheet);
    }

    /**
     * 載入參數
     *
     * @param array $options
     *            參數
     */
    public function setOptions(Array $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * 載入資料
     *
     * @param string $data
     *            資料
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * 載入結構定義
     *
     * @param object $config
     *            定義檔
     */
    public function setConfig($config)
    {
        // 設定檔物件
        $this->_config = $config;
        // 載入下拉選單定義
        foreach ($this->_listMap as $k => $map) {
            $this->_config->setList($k, $map);
        }
        // 傳址對應
        $this->_listMap = & $this->_config->getList();
        return $this;
    }

    /**
     * 載入樣式定義
     *
     * @param object $style
     *            定義檔
     */
    public function setStyle($style)
    {
        $this->_style = $style;
        return $this;
    }

    /**
     * 載入下拉選單定義 - 額外定義資料
     *
     * @param string $list
     *            定義檔
     */
    public function setList($keyName, $listDEfined)
    {
        $this->_listMap[$keyName] = $listDEfined;
        return $this;
    }

    /**
     * 輸出資料 - 匯出成品
     *
     * @param string $name            
     */
    public function output($name = '')
    {
        $name = ($name) ? $name : $this->_options['fileName'];
        
        $this->_builder->setSheet(1);
        $this->_builder->output($name);
    }

    /**
     * 輸出資料 - 取得資料 - 原始資料/匯入成品
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * 載入下拉選單定義 - 額外定義資料
     *
     * @param string $list
     *            定義檔
     */
    public function getList()
    {
        return $this->_listMap;
    }

    /**
     * ***********************************************
     * ************** Building Function **************
     * ***********************************************
     */
    
    /**
     * 建構資料
     *
     * @return \marshung\io\builder\ExcelBuilder
     */
    public function build()
    {
        // 參數工作表設定
        $this->optionBuilder();
        
        // 標題建構
        $this->titleBuilder();
        
        // 內容建構
        $this->contentBuyilder();
        
        // 結尾建構
        $this->footBuilder();
        
        // 樣式建構Style
        $this->styleBuilder();
        
        // 下拉選單建構
        $this->listBuilder();
        
        return $this;
    }

    /**
     * 參數工作表設定
     *
     * @param bool $inBuilder
     *            是否在builder內部執行，預設true
     */
    public function optionBuilder($inBuilder = true)
    {
        if ($inBuilder) {
            // 第一張表更名為設工作表 ConfigSheet
            $configSheet = $this->_builder->setSheet(0, 'ConfigSheet')->getSheet();
        } else {
            // 外部呼叫執行的
            $configSheet = $this->_builder->getSheet('ConfigSheet', true);
        }
        
        // ====== 參數工作表格式 ======
        // 保護工作表
        $configSheet->getProtection()->setSheet(true);
        // 隱藏工作表
        $configSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VERYHIDDEN);
        // 設定A欄寬度
        $configSheet->getDefaultColumnDimension()->setWidth('15');
        $configSheet->getColumnDimension('A')->setWidth('25');
        // 預設儲存格格式:文字
        $this->_builder->getSpreadsheet()
            ->getDefaultStyle()
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        // ======
        
        // ====== 參數工作表內容 - 基本參數 ======
        if (is_object($this->_config)) {
            // 取得設定檔參數
            $options = $this->_config->getOption();
            // 基本參數設定 - 第一列:設定檔參數、第二列:建構函式參數
            $this->_builder->addRows([
                $options,
                $this->_options
            ]);
        }
        // ======
        
        return $this;
    }

    /**
     * 標題建構
     */
    public function titleBuilder()
    {
        // 取得設定檔參數-工作表名稱
        $sheetName = $this->_config->getOption('sheetName');
        
        // 取得工作表
        $sheet = $this->_builder->getSheet($sheetName, true);
        
        // 起始座標
        $colStart = 'A';
        $rowStart = '1';
        // 結束座標 - 初始化
        $colEnd = 'A';
        $rowEnd = '0';
        
        // 建構標題資料
        foreach ($this->_config->getTitle() as $key => $tRow) {
            // 設定資料過濾，在喂給helper時不會有多餘的資料
            $this->_config->definedFilter($tRow);
            $rowData = $this->_config->getRowFromDefined($tRow);
            
            // 寫入標題資料至excel
            $this->_builder->addRows([
                $rowData
            ]);
            
            // 取得本次結束座標
            $colEnd = $this->_builder->getSheet()->getHighestColumn();
            $rowEnd = $this->_builder->getSheet()->getHighestRow();
            
            // 座標記錄 - 每個子標題 - 樣式
            $this->offsetMapSet('title', $tRow, $colStart, $rowEnd, $colEnd, $rowEnd);
        }
        
        // 座標記錄 - 全標題
        if ($rowEnd) {
            $this->offsetMapSet('title', 'all', $colStart, $rowStart, $colEnd, $rowEnd);
        }
    }

    /**
     * 內容建構
     */
    public function contentBuyilder()
    {
        // 取得定義資料
        $content = $this->_config->getContent();
        
        if (! empty($content)) {
            // 重整內容資料
            $this->_rebuildContent();
        }
        
        // 起始座標
        $colStart = 'A';
        $rowStart = sizeof($this->_config->getTitle());
        $rowStart ++;
        // 結束座標 - 初始化
        $colEnd = 'A';
        $rowEnd = '0';
        
        // 建構內容資料
        $this->_builder->addRows($this->_data);
        
        // 結束座標
        $colEnd = $this->_builder->getSheet()->getHighestColumn();
        $rowEnd = $this->_builder->getSheet()->getHighestRow();
        
        if ($rowEnd) {
            // 座標記錄 - 全內容 - 樣式
            $this->offsetMapSet('content', $content, $colStart, $rowStart, $colEnd, $rowEnd);
            // 座標記錄 - 全內容
            $this->offsetMapSet('content', 'all', $colStart, $rowStart, $colEnd, $rowEnd);
        }
    }

    /**
     * 結尾建構
     */
    public function footBuilder()
    {
        // 起始座標
        $colStart = 'A';
        $rowStart = $this->_builder->getSheet()->getHighestRow();
        $rowStart ++;
        // 結束座標 - 初始化
        $colEnd = 'A';
        $rowEnd = '0';
        
        // 建構標題資料
        foreach ($this->_config->getFoot() as $key => $fRow) {
            // 設定資料過濾，在喂給helper時不會有多餘的資料
            $this->_config->definedFilter($fRow);
            $rowData = $this->_config->getRowFromDefined($fRow);
            
            // 寫入標題資料至excel
            $this->_builder->addRows([
                $rowData
            ]);
            
            // 取得本次結束座標
            $colNow = $this->_builder->getSheet()->getHighestColumn();
            $rowNow = $this->_builder->getSheet()->getHighestRow();
            
            // 座標記錄 - 每個子結尾 - 樣式
            $this->offsetMapSet('foot', $fRow, $colStart, $rowEnd, $colEnd, $rowEnd);
        }
        
        // 座標記錄 - 全結尾
        if ($rowEnd) {
            $this->offsetMapSet('foot', 'all', $colStart, $rowStart, $colEnd, $rowEnd);
        }
        
        // 結束座標
        $colEnd = $this->_builder->getSheet()->getHighestColumn();
        $rowEnd = $this->_builder->getSheet()->getHighestRow();
        
        // 座標記錄 - 全工作表
        if ($rowEnd) {
            $this->offsetMapSet('sheet', 'all', $colStart, '1', $colEnd, $rowEnd);
        }
    }

    /**
     * 樣式建構Style
     */
    public function styleBuilder()
    {
        // Excel物件暫存資料
        $spreadsheet = $this->_builder->getSpreadsheet();
        // 取得工作表
        $sheet = $this->_builder->getSheet();
        
        // ====== 建立樣式-類型 ======
        // 建立Excel樣式 - 預設樣式
        $defaultStyle = $this->_style->getDefault();
        \marshung\io\builder\ExcelStyleBuilder::setExcelDefaultStyle($defaultStyle, $spreadsheet);
        
        // 建立Excel樣式 - 標題樣式
        $titleStyle = $this->_style->getTitle();
        $titleRange = $this->offsetMap('title');
        \marshung\io\builder\ExcelStyleBuilder::setExcelRangeStyle($titleStyle, $spreadsheet, $titleRange);
        
        // 建立Excel樣式 - 內容樣式
        $contentStyle = $this->_style->getContent();
        $contentRange = $this->offsetMap('content');
        \marshung\io\builder\ExcelStyleBuilder::setExcelRangeStyle($contentStyle, $spreadsheet, $contentRange);
        
        // 建立Excel樣式 - 結尾樣式
        $footStyle = $this->_style->getFoot();
        $footRange = $this->offsetMap('foot');
        \marshung\io\builder\ExcelStyleBuilder::setExcelRangeStyle($footStyle, $spreadsheet, $footRange);
        // ======
        
        // ====== 建立樣式-從Config ======
        // === 標題設定 ===
        $config = $this->_config->getTitle();
        foreach ($config as $idx => $conf) {
            // 樣式建構Style - 從Config
            $this->_configStyleBuilder($conf, $spreadsheet);
        }
        
        // === 內容設定 ===
        $config = $this->_config->getContent();
        // 樣式建構Style - 從Config
        $this->_configStyleBuilder($config, $spreadsheet);
        
        // === 結尾設定 ===
        $config = $this->_config->getFoot();
        foreach ($config as $idx => $conf) {
            // 樣式建構Style - 從Config
            $this->_configStyleBuilder($conf, $spreadsheet);
        }
        // ======
        
        // ====== 處理凍結欄位 ======
        // 取得樣式 - 凍結欄位
        $freezeCol = $this->_style->getFreeze();
        if ($freezeCol) {
            $contentRange = $this->offsetMap('content', 'rowStart');
            $freezeCell = $freezeCol . $contentRange;
            // 凍結欄位
            \marshung\io\builder\ExcelStyleBuilder::setFreeze($freezeCell, $spreadsheet);
        }
        // ======
    }

    /**
     * 下拉選單建構
     *
     * 1. 有內容定義時，使用內容定義處理下拉選單
     * 2. 無內容定義時，使用傳入資料處理下拉選單
     */
    public function listBuilder()
    {
        // 記錄原工作表索引 - 取得下拉選單的目標工作表索引
        $sheet = $this->_builder->getSheet();
        
        // ========== 建構下拉選單 ==========
        // 取得內容列數起訖
        $rowStart = $this->offsetMap('content', 'rowStart');
        $rowEnd = $this->offsetMap('content', 'rowEnd');
        
        // 取得內容定義資料
        $content = $this->_config->getContent();
        if (! empty($content)) {
            // 有內容定義 - 取得欄位定義
            $cDefined = $this->_config->getRowFromDefined($content);
        } else {
            // 沒有內容定義，改用傳入資料陣列
            $cDefined = $this->_data;
        }
        if (is_array(current($cDefined))) {
            $cDefined = array_column($cDefined, 'value', 'key');
        }
        
        // 遍歷資料範本 - 建構下拉選單值的資料表，並繫結到目標欄位
        $colCount = 0;
        foreach ($cDefined as $key => $colTitle) {
            // 欄位計算
            $colCount ++;
            // 跳過不處理的欄位
            if (! isset($this->_listMap[$key])) {
                continue;
            }
            
            
            // ====== 將下拉選單繫結到目標工作表 ======
            // 取得資料Key對映的Excel欄位碼 - 簡易模式或傳入資料陣列時，欄位碼需計算
            $colCode = $this->_builder->getColumnMap($key);
            $colCode = empty($colCode) ? $this->_builder->num2alpha($colCount) : $colCode;
            // 遍歷目標欄位的各cell - 下拉選單需一cell一cell的繫結
            for ($i = $rowStart; $i <= $rowEnd; $i ++) {
                // 對指定欄位建構下拉選單結構
                $this->listSet($sheet, $key, $colCode . $i, $colTitle);
            }
            // ======
        }
        // ==========
        
        return $this;
    }

    /**
     * ********************************************
     * ************** Parse Function **************
     * ********************************************
     */
    
    /**
     * 解析匯入資料並回傳
     *
     * @throws \Exception
     * @return \marshung\io\builder\ExcelBuilder
     */
    public function parse()
    {
        // 取得參數資料 - 設定檔參數
        $sheet = $this->_builder->getSheet('ConfigSheet');
        if (is_null($sheet)) {
            throw new \Exception('參數表錯誤', 404);
        }
        
        $config = $this->_builder->getRow();
        
        // 參數解析
        $config = $this->_config->optionParser($config);
        
        if ($config === false) {
            throw new \Exception('參數表錯誤', 404);
        }
        
        // 取得標題設定
        $title = $this->_config->getTitle();
        $titleRowNumber = sizeof($title);
        // 取得結尾設定
        $foot = $this->_config->getFoot();
        $footRowNumber = sizeof($foot);
        
        // 取得原始資料
        $data = array();
        $sheet = $this->_builder->getSheet($config['sheetName']);
        $this->_builder->setSheet($sheet);
        while ($row = $this->_builder->getRow()) {
            // 略過不要的資料 - 標題
            if ($titleRowNumber > 0) {
                $titleRowNumber --;
                continue;
            }
            // 取得資料
            $data[] = $row;
        }
        
        // 去除結尾資料
        if ($footRowNumber) {
            $length = sizeof($data) - $footRowNumber;
            $data = array_slice($data, 0, $length);
        }
        
        // 匯入資料解析
        $this->_config->contentParser($data);
        
        // 回寫資料
        $this->_data = $data;
        
        return $this;
    }

    /**
     * *********************************************
     * ************** Offset Function **************
     * *********************************************
     */
    
    /**
     * 取得座標記錄內容
     *
     * @param string $type
     *            定義種類名稱 title,content,foot,sheet
     * @param string $content
     *            座標格式類型 colStart,rowStart,colEnd,rowEnd,range
     * @param string $configName
     *            定義名稱 取全域$type資料時，值為null，結構定義物件使用複雜模式才有
     */
    public function offsetMap($type, $content = 'range', $configName = null)
    {
        if (is_null($configName)) {
            // 取全域資料
            return isset($this->_offsetMap[$type]['all'][$content]) ? $this->_offsetMap[$type]['all'][$content] : NULL;
        } else {
            // 取子定義資料
            return isset($this->_offsetMap[$type]['detail'][$configName][$content]) ? $this->_offsetMap[$type]['detail'][$configName][$content] : NULL;
        }
    }

    /**
     * 座標記錄
     *
     * @param string $type
     *            定義種類名稱 title,content,foot,sheet
     * @param string $config
     *            內容，如果為全域$type時，類型為string
     * @param string $colStart
     *            起始欄
     * @param string $rowStart
     *            起始列
     * @param string $colEnd
     *            結束欄
     * @param string $rowEnd
     *            結束列
     */
    protected function offsetMapSet($type, $config, $colStart, $rowStart, $colEnd, $rowEnd)
    {
        // 座標錯誤，跳出
        if ($rowEnd < $rowStart || $colEnd < $colStart) {
            return false;
        }
        
        // 建構座標內容
        $conf = array(
            'configName' => '',
            'colStart' => $colStart,
            'rowStart' => $rowStart,
            'colEnd' => $colEnd,
            'rowEnd' => $rowEnd,
            'range' => $colStart . $rowStart . ':' . $colEnd . $rowEnd
        );
        
        // 設定座標記錄
        if (is_string($config)) {
            // 全域定義種類
            $this->_offsetMap[$type]['all'] = $conf;
        } else {
            // 子定義種類 - 結構定義物件複雜模式才有
            $conf['configName'] = $configName = isset($config['config']['name']) ? $config['config']['name'] : '';
            // 檢查定義名稱是否重複
            if (isset($this->_offsetMap[$type]['detail'][$configName])) {
                throw new \Exception('Config Name Duplicate: ' . $configName, 404);
            }
            $this->_offsetMap[$type]['detail'][$configName] = $conf;
        }
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
    /**
     * 重整內容資料
     */
    protected function _rebuildContent()
    {
        // 內容整併 - 以資料內容範本為模版合併資料 - 欄位、排序、預設值、資料轉換
        $this->_config->contentRefactor($this->_data)->contentFilter($this->_data);
        
        // 取得定義資料
        $content = $this->_config->getContent();
        
        // 設定資料過濾，在喂給helper時不會有多餘的資料
        $this->_config->definedFilter($content);
        $rowData = $this->_config->getRowFromDefined($content);
        
        // 整理資料 - 依欄位設定
        $tmpData = array();
        foreach ($this->_data as $k => $row) {
            // 重寫欄位定義的value - 保持定義格式
            foreach ($row as $key => $col) {
                if (isset($rowData[$key])) {
                    if (isset($rowData[$key]['value'])) {
                        // 模式：複雜(complex)
                        $rowData[$key]['value'] = $col;
                    } else {
                        // 模式：簡易(simple)
                        $rowData[$key] = $col;
                    }
                }
            }
            $tmpData[$k] = $rowData;
        }
        
        // 回寫
        $this->_data = $tmpData;
        
        return $this->_data;
    }

    /**
     * 樣式建構Style - 從Config
     */
    protected function _configStyleBuilder($config, &$spreadsheet)
    {
        // 簡易模式，不處理
        if (! isset($config['config'])) {
            return $this;
        }
        
        // 設定資料類型
        $blockType = $config['config']['type'];
        
        // === 全設定區塊 ===
        // 取得儲存格範圍
        $blockName = $config['config']['name'];
        $blockRange = $this->offsetMap($blockType, 'range', $blockName);
        
        // 取得樣式資料
        $style = $config['config']['style'];
        $className = $config['config']['class'];
        $class = $this->_style->getClass($className);
        
        // 設定style樣式
        \marshung\io\builder\ExcelStyleBuilder::setExcelRangeStyle($style, $spreadsheet, $blockRange);
        
        // 設定Class樣式
        \marshung\io\builder\ExcelStyleBuilder::setExcelRangeStyle($class, $spreadsheet, $blockRange);
        
        // === 欄位 ===
        foreach ($config['defined'] as $idx => $conf) {
            // 取得儲存格範圍
            // 取得資料Key對映的Excel欄位碼
            $keyName = $conf['key'];
            $colCode = $this->_builder->getColumnMap($keyName);
            // 取得內容列數起訖
            $rowStart = $this->offsetMap($blockType, 'rowStart', $blockName);
            $rowEnd = $this->offsetMap($blockType, 'rowEnd', $blockName);
            $blockRange = $colCode . $rowStart . ':' . $colCode . $rowEnd;
            
            // 取得樣式資料
            $style = isset($conf['style']) ? $conf['style'] : array();
            $className = isset($conf['class']) ? $conf['class'] : '';
            $class = $this->_style->getClass($className);
            
            // 設定style樣式
            \marshung\io\builder\ExcelStyleBuilder::setExcelRangeStyle($style, $spreadsheet, $blockRange);
            
            // 設定Class樣式
            \marshung\io\builder\ExcelStyleBuilder::setExcelRangeStyle($class, $spreadsheet, $blockRange);
        }
    }

    /**
     * 將下拉選單資料建構成下拉選單結構資料表
     */
    protected function _listAddrMapBuilder()
    {
        // 記錄原工作表索引 - 取得下拉選單的目標工作表索引
        $origSheet = $this->_builder->getSheet();
        // 取得參數工作表
        $configSheet = $this->_builder->getSheet('ConfigSheet');
        
        if (! $configSheet) {
            // 找不到參數工作表 - 參數工作表設定
            $this->optionBuilder($inBuilder = false);
            // 取得參數工作表
            $configSheet = $this->_builder->getSheet('ConfigSheet');
        }
        
        // 取得參數工作表目前列數
        $csRow = $configSheet->getHighestRow();
        
        // ====== 將下拉選單項目寫到參數工作表 ======
        foreach ($this->_listMap as $key => $listDef) {
            // 取得下拉選單項目定義資料
            $listItem = array_column($listDef, 'text');
            // 將下拉選單名稱、下拉選單定義合併，名稱在第一欄
            $listItem = array_merge(array(
                $key
            ), $listItem);
            // 將下拉選單項目寫到參數工作表
            $this->_builder->setSheet($configSheet)
                ->setRowOffset($csRow)
                ->addRows([
                $listItem
            ]);
            // 更新參數工作列數
            $csRow = $configSheet->getHighestRow();
            // 計算定義佔用的欄數，並取得該欄的代碼
            $lastColCode = $this->_builder->num2alpha(sizeof($listItem));
            
            // 建立下拉選單結構位址
            $this->_listAddrMap[$key] = $configSheet->getTitle() . '!$B$' . $csRow . ':$' . $lastColCode . '$' . $csRow;
        }
        // ======
        
        // 回原工作表
        $this->_builder->setSheet($origSheet);
        
        return $this;
    }

    /**
     * 對指定欄位建構下拉選單結構
     *
     * @param object $sheet
     *            目標工作表物件
     * @param string $listKey
     *            下拉選單名稱
     * @param string $cellLocation
     *            欄位座標
     * @param string $cellTitle
     *            欄位名稱，可省略
     */
    public function listSet($sheet, $listKey, $cellLocation, $cellTitle = null)
    {
        // 如果沒有初始化下拉選單結構資料表，執行它
        if (empty($this->_listAddrMap)) {
            // 將下拉選單資料建構成下拉選單結構資料表
            $this->_listAddrMapBuilder();
        }
        
        // 參數設定 - 無標題時預設為key名稱
        $cellTitle = is_string($cellTitle) ? $cellTitle : $listKey;
        
        // 有下拉選單結構時才處理
        if (isset($this->_listAddrMap[$listKey])) {
            // 對指定欄位建構下拉選單結構
            $sheet->getCell($cellLocation)
                ->getDataValidation()
                ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
                ->setAllowBlank(false)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setErrorTitle('輸入的值有誤')
                ->setError('您輸入的值不在下拉框列表內.')
                ->setPromptTitle($cellTitle)
                ->setFormula1($this->_listAddrMap[$listKey]);
        }
        
        return $this;
    }
}

<?php
namespace app\libraries\io\builder;

/**
 * NuEIP IO Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class ExcelBuilder
{
    
    /**
     * 資料
     *
     * @var array
     */
    protected $_version = '0.1';
    
    /**
     * 資料
     *
     * @var array
     */
    protected $_data;

    /**
     * 定義資料
     * 
     * @var object
     */
    protected $_config;
    
    /**
     * Style定義資料
     *
     * @var object
     */
    protected $_style;
    
    /**
     * 下拉選單定義資料
     *
     * @var array $list[$key] = array('value' => '值', 'text' => '文字', 'type' => '資料類型');
     */
    protected $_listMap;
    
    /**
     * 輸出暫存資料
     *
     * @var object
     */
    protected $_builder;
    
    /**
     * 欄位座標參數表
     *
     * @var array
     */
    protected $_offsetMap;
    

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        // 初始化
        $this->_builder = \yidas\phpSpreadsheet\Helper::newSpreadsheet();
        $this->_builder->getSpreadsheet()->getProperties()
            ->setCreator("NuEIP")
            ->setTitle("Office 2007 XLSX Document");
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
     * @param string $config
     *            定義檔
     */
    public function setConfig($config)
    {
        // 設定檔物件
        $this->_config = $config;
        // 載入下拉選單定義
        $this->_listMap = $this->_config->getList();
        return $this;
    }
    
    /**
     * 載入樣式定義
     *
     * @param string $style
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

    public function build()
    {
        getRunTime();
        
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
        
//         echo getRunTime();
//         exit;
        
        return $this;
    }

    public function output()
    {
        $this->_builder->setSheet(0);
        $this->_builder->output();
    }

    /**
     * ***********************************************
     * ************** Building Function **************
     * ***********************************************
     */
    
    /**
     * 標題建構
     */
    public function titleBuilder()
    {
        
        // 取得工作表
        $sheet = $this->_builder->getSheet();
        
        // 起始座標
        $colStart = 'A';
        $rowStart = '1';
        // 結束座標 - 初始化
        $colEnd = 'A';
        $rowEnd = '0';
        
        // 建構標題資料
        foreach ($this->_config->title() as $key => $tRow) {
            // 寫入標題資料至excel
            $this->_builder->addRows([$tRow['defined']]);
            
            // 取得本次結束座標
            $colEnd = $this->_builder->getSheet()->getHighestColumn();
            $rowEnd = $this->_builder->getSheet()->getHighestRow();
            
            // 座標記錄 - 每個子標題 - 樣式
            $this->offsetMapSet('title', $tRow, $colStart, $rowEnd, $colEnd, $rowEnd);
        }
        
        // 座標記錄 - 全標題
        $this->offsetMapSet('title', 'all', $colStart, $rowStart, $colEnd, $rowEnd);
    }

    /**
     * 內容建構
     */
    public function contentBuyilder()
    {
        // 重整內容資料
        $this->_rebuildContent();
        
        // 取得定義資料
        $content = $this->_config->content();
        
        // 起始座標
        $colStart = 'A';
        $rowStart = $this->_builder->getSheet()->getHighestRow();
        $rowStart++;
        // 結束座標 - 初始化
        $colEnd = 'A';
        $rowEnd = '0';
        
        
        // 建構內容資料
        $this->_builder->addRows($this->_data);
        
        // 結束座標
        $colEnd = $this->_builder->getSheet()->getHighestColumn();
        $rowEnd = $this->_builder->getSheet()->getHighestRow();
        
        // 座標記錄 - 全內容 - 樣式
        $this->offsetMapSet('content', $content, $colStart, $rowStart, $colEnd, $rowEnd);
        // 座標記錄 - 全內容
        $this->offsetMapSet('content', 'all', $colStart, $rowStart, $colEnd, $rowEnd);
    }
    
    /**
     * 結尾建構
     */
    public function footBuilder()
    {
        // 起始座標
        $colStart = 'A';
        $rowStart = $this->_builder->getSheet()->getHighestRow();
        $rowStart++;
        // 結束座標 - 初始化
        $colEnd = 'A';
        $rowEnd = '0';
        
        // 建構標題資料
        foreach ($this->_config->foot() as $key => $fRow) {
            // 寫入標題資料至excel
            $this->_builder->addRows([$fRow['defined']]);
            
            // 取得本次結束座標
            $colNow = $this->_builder->getSheet()->getHighestColumn();
            $rowNow = $this->_builder->getSheet()->getHighestRow();
            
            // 座標記錄 - 每個子結尾 - 樣式
            $this->offsetMapSet('foot', $fRow, $colStart, $rowEnd, $colEnd, $rowEnd);
        }
        
        // 座標記錄 - 全結尾
        $this->offsetMapSet('foot', 'all', $colStart, $rowStart, $colEnd, $rowEnd);
        
        // 結束座標
        $colEnd = $this->_builder->getSheet()->getHighestColumn();
        $rowEnd = $this->_builder->getSheet()->getHighestRow();
        
        // 座標記錄 - 全工作表
        $this->offsetMapSet('sheet', 'all', $colStart, '1', $colEnd, $rowEnd);
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
        $sheetRange = $this->offsetMap('sheet');
        \app\libraries\io\builder\ExcelStyleBuilder::setExcelDefaultStyle($defaultStyle, $spreadsheet);
        
        // 建立Excel樣式 - 標題樣式
        $titleStyle = $this->_style->getTitle();
        $titleRange = $this->offsetMap('title');
        \app\libraries\io\builder\ExcelStyleBuilder::setExcelRangeStyle($titleStyle, $spreadsheet, $titleRange);
        
        // 建立Excel樣式 - 內容樣式
        $contentStyle = $this->_style->getContent();
        $contentRange = $this->offsetMap('content');
        \app\libraries\io\builder\ExcelStyleBuilder::setExcelRangeStyle($contentStyle, $spreadsheet, $contentRange);
        
        // 建立Excel樣式 - 結尾樣式
        $footStyle = $this->_style->getFoot();
        $footRange = $this->offsetMap('foot');
        \app\libraries\io\builder\ExcelStyleBuilder::setExcelRangeStyle($footStyle, $spreadsheet, $footRange);
        // ======
        
        // ====== 建立樣式-從Config ======
        // === 標題設定 ===
        $config = $this->_config->title();
        foreach ($config as $idx => $conf) {
            // 樣式建構Style - 從Config
            $this->_configStyleBuilder($conf, $spreadsheet);
        }
        
        // === 內容設定 ===
        $config = $this->_config->content();
        // 樣式建構Style - 從Config
        $this->_configStyleBuilder($config, $spreadsheet);
        
        // === 結尾設定 ===
        $config = $this->_config->foot();
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
            $freezeCell = $freezeCol.$contentRange;
            // 凍結欄位
            \app\libraries\io\builder\ExcelStyleBuilder::setFreeze($freezeCell, $spreadsheet);
        }
        // ======
    }
    
    /**
     * 下拉選單建構
     */
    public function listBuilder()
    {
        // 記錄原工作表索引 - 取得下拉選單的目標工作表索引
        $origSheetIndex = $this->_builder->getActiveSheetIndex();
        
        
        // ====== 取得參數工作表 ======
        // 取得工作表數量
        $sheetCount = $this->_builder->getSheetCount();
        // 新增參數Sheet - 下拉選單對映表sheet
        $this->_builder->setSheet($sheetCount, 'ConfigSheet');
        // 記錄參數工作表索引
        $configSheetIndex = $this->_builder->getActiveSheetIndex();
        // 取得參數工作表
        $configSheet = $this->_builder->getSheet();
        // ======
        
        
        // ====== 參數工作表格式 ======
        // 保護工作表
        $configSheet->getProtection()->setSheet(true);
        // 隱藏工作表
        $configSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VERYHIDDEN);
        // 設定A欄寬度
        $configSheet->getDefaultColumnDimension()->setWidth('15');
        $configSheet->getColumnDimension('A')->setWidth('25');
        // 預設儲存格格式:文字
        $this->_builder->getSpreadsheet()->getDefaultStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        // ======
        
        
        // ====== 參數工作表內容 - 基本參數 ======
        // 基本參數設定
        $this->_builder->addRows([
            [
                '匯入功能名稱',
                'AddIns',
            ],
            [
                '版本資訊',
                $this->_version
            ]
        ]);
        // ======
        
        
        // 建構下拉選單
        $this->_listBuilder($origSheetIndex, $configSheetIndex);
        
        // 回原工作表
        $this->_builder->setSheet($origSheetIndex);
        
        return ;
        
        // 取得工作表 - 從名子
        $spreadsheet = $this->_builder->getSpreadsheet()->getSheetByName('ConfigSheete');
    }

    /**
     * *********************************************
     * ************** Offset Function **************
     * *********************************************
     */
    
    /**
     * 取得座標記錄內容
     *
     * @param string $type 定義種類名稱 title,content,foot,sheet
     * @param string $content 座標格式類型 colStart,rowStart,colEnd,rowEnd,range
     * @param string $configName 定義名稱 取全域$type資料時，值為null
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
     * @param string $type 定義種類名稱 title,content,foot,sheet
     * @param string $config 內容，如果為全域$type時，類型為string
     * @param string $colStart 起始欄
     * @param string $rowStart 起始列
     * @param string $colEnd 結束欄
     * @param string $rowEnd 結束列
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
            'range' => $colStart.$rowStart.':'.$colEnd.$rowEnd
        );
        
        // 設定座標記錄
        if (is_string($config)) {
            // 全域定義種類
            $this->_offsetMap[$type]['all'] = $conf;
        } else {
            // 子定義種類
            $conf['configName'] = $configName = isset($config['config']['name']) ? $config['config']['name'] : '';
            // 檢查定義名稱是否重複
            if (isset($this->_offsetMap[$type]['detail'][$configName])) {
                throw new \Exception('Config Name Duplicate: '.$configName, 404);
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
        // 取得定義資料
        $content = $this->_config->content();
        $cDefined = $content['defined'];
        
        // 重建索引 - 依Key
        \nueip\helpers\ArrayHelper::indexBy($cDefined, 'key');
        
        // 整理資料 - 依欄位設定
        $tmpData = array();
        foreach ($this->_data as $k => $row) {
            // 重寫欄位定義的value - 保持定義格式
            foreach ($row as $key => $col) {
                if (isset ($cDefined[$key])) {
                    $cDefined[$key]['value'] = $col;
                }
            }
            
            $tmpData[$k] = $cDefined;
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
        \app\libraries\io\builder\ExcelStyleBuilder::setExcelRangeStyle($style, $spreadsheet, $blockRange);
        
        // 設定Class樣式
        \app\libraries\io\builder\ExcelStyleBuilder::setExcelRangeStyle($class, $spreadsheet, $blockRange);
        
        // === 欄位 ===
        foreach ($config['defined'] as $idx => $conf) {
            // 取得儲存格範圍
            // 取得資料Key對映的Excel欄位碼
            $keyName = $conf['key'];
            $colCode = $this->_builder->getColumnMap($keyName);
            // 取得內容列數起訖
            $rowStart = $this->offsetMap($blockType, 'rowStart', $blockName);
            $rowEnd= $this->offsetMap($blockType, 'rowEnd', $blockName);
            $blockRange = $colCode.$rowStart.':'.$colCode.$rowEnd;
            
            // 取得樣式資料
            $style = isset($conf['style']) ? $conf['style'] : array();
            $className = isset($conf['class']) ? $conf['class'] : '';
            $class = $this->_style->getClass($className);
            
            // 設定style樣式
            \app\libraries\io\builder\ExcelStyleBuilder::setExcelRangeStyle($style, $spreadsheet, $blockRange);
            
            // 設定Class樣式
            \app\libraries\io\builder\ExcelStyleBuilder::setExcelRangeStyle($class, $spreadsheet, $blockRange);
        }
    }
    
    
    /**
     * 建構下拉選單
     * 
     * @param int $origSheetIndex 原工作表索引，即下拉選單的目標
     * @param int $configSheetIndex 參數工作表索引
     */
    protected function _listBuilder($origSheetIndex, $configSheetIndex)
    {
        // 取得目標工作表、參數工作表
        $configSheet = $this->_builder->setSheet($configSheetIndex)->getSheet();
        $origSheet = $this->_builder->setSheet($origSheetIndex)->getSheet();
        
        // 取得內容列數起訖
        $rowStart = $this->offsetMap('content', 'rowStart');
        $rowEnd= $this->offsetMap('content', 'rowEnd');
        
        // 取得參數工作表目前列數
        $csRow = $configSheet->getHighestRow();
        
        // 取得下拉選單設定
        $listMap = $this->_listMap;
        
        // 取得內容定義資料
        $content = $this->_config->content();
        $cDefined = array_column($content['defined'], 'value', 'key');
        
        // 遍歷資料範本 - 建構下拉選單值的資料表，並繫結到目標欄位
        foreach ($cDefined as $key => $colTitle) {
            // 跳過不處理的欄位
            if (!isset($listMap[$key])) {
                continue;
            }
            
            
            // ====== 將下拉選單項目寫到參數工作表 ======
            // 取得下拉選單項目定義資料
            $listItem = array_column($listMap[$key], 'text');
            // 將下拉選單名稱、下拉選單定義合併，名稱在第一欄
            $listItem = array_merge(array(
                $colTitle
            ), $listItem);
            // 將下拉選單項目寫到參數工作表
            $this->_builder->setSheet($configSheet)->setRowOffset($csRow)->addRows([
                $listItem
            ]);
            // 更新參數工作列數
            $csRow = $configSheet->getHighestRow();
            // 計算定義佔用的欄數，並取得該欄的代碼
            $lastColCode = $this->_builder->num2alpha(sizeof($listItem));
            // ======
            
            
            // ====== 將下拉選單繫結到目標工作表 ======
            // 取得資料Key對映的Excel欄位碼
            $colCode = $this->_builder->getColumnMap($key);
            $this->_builder->setSheet($origSheet);
            // 遍歷目標欄位的各cell - 下拉選單需一cell一cell的繫結
            for ($i = $rowStart; $i <= $rowEnd; $i ++) {
                $origSheet->getCell($colCode. $i)
                ->getDataValidation()
                ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
                ->setAllowBlank(false)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setErrorTitle('輸入的值有誤')
                ->setError('您輸入的值不在下拉框列表內.')
                ->setPromptTitle($colTitle)
                ->
                // 把sheet名为mySheet2的A1，A2,A3作为选项
                setFormula1($configSheet->getTitle() . '!$B$' . $csRow . ':$' . $lastColCode . '$' . $csRow);
            }
            // ======
        }
    }
}

<?php
namespace app\libraries\io2\builder;

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
    protected $_list;
    
    /**
     * 輸出暫存資料
     *
     * @var object
     */
    protected $_builder;
    
    /**
     * Excel物件暫存資料
     *
     * @var array
     */
    protected $_spreadsheet;
    
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
        $this->_config = $config;
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
     * 載入下拉選單定義
     *
     * @param string $list
     *            定義檔
     */
    public function setList($list)
    {
        $this->_list = $list;
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
        $this->rebuildContent();
        
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
        $this->_spreadsheet = $this->_builder->getSpreadsheet();
        // 取得工作表
        $sheet = $this->_builder->getSheet();
        
        // ====== 取得樣式 ======
        // 取得預設樣式
        $defaultStyle = $this->_style->getDefault();
        // 取得標題樣式
        $titleStyle = $this->_style->getTitle();
        // 取得內容樣式
        $contentStyle = $this->_style->getContent();
        // 取得結尾樣式
        $footStyle = $this->_style->getFoot();
        // ======

        // ====== 取得座標 ======
        $sheetRange = $this->offsetMap('sheet');
        $titleRange = $this->offsetMap('title');
        $contentRange = $this->offsetMap('content');
        $footRange = $this->offsetMap('foot');
        // ======
        
        // ====== 建立樣式 ======
        // 建立Excel樣式 - 預設樣式
        \app\libraries\io2\builder\ExcelStyleBuilder::setExcelDefaultStyle($defaultStyle, $this->_spreadsheet);
        // 建立Excel樣式 - 標題樣式
        \app\libraries\io2\builder\ExcelStyleBuilder::setExcelRangeStyle($titleStyle, $this->_spreadsheet, $titleRange);
        // 建立Excel樣式 - 內容樣式
        \app\libraries\io2\builder\ExcelStyleBuilder::setExcelRangeStyle($contentStyle, $this->_spreadsheet, $contentRange);
        // 建立Excel樣式 - 結尾樣式
        \app\libraries\io2\builder\ExcelStyleBuilder::setExcelRangeStyle($footStyle, $this->_spreadsheet, $footRange);
        // ======
        
//         // ====== 處理凍結欄位 ======
//         // 取得樣式 - 凍結欄位
//         $freezeCol = $this->_style->getFreeze();
//         if ($freezeCol) {
//             $contentRange = $this->offsetMap('content', 'rowStart');
//             $freezeCell = $freezeCol.$contentRange;
//             // 凍結欄位
//             \app\libraries\io2\builder\ExcelStyleBuilder::setFreeze($freezeCell, $this->_spreadsheet);
//         }
//         // ======
        
        // 取得工作表
//         $sheet = $this->_builder->getSheet();
        
//         $cellRange = 'A1:B2';
//         dump($sheet->getColumnDimension($cellRange));
        
//         exit;
        
        
        // 
        
//         var_export($defaultStyle);
        
//         exit;
        
//         var_export($default);
        
        
        
        
        
// //         dump($this->_style);
//         exit;
        
        
        
        
        
    }
    
    /**
     * 下拉選單建構
     */
    public function listBuilder()
    {}

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
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
     * 
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
            'styleName' => '',
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
            $conf['styleName'] = isset($config['config']['style']) ? $config['config']['style'] : '';
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
    protected function rebuildContent()
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
    
}

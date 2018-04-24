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
     * 工作表暫存資料
     *
     * @var array
     */
    protected $_sheet;
    

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
        
        // 建構標題資料
        foreach ($this->_config->title() as $key => $tRow) {
            $this->_builder->addRows([$tRow['defined']]);
        }
        
//         // 取得工作表
//         $this->_sheet = $this->_builder->getSheet();
//         $a = $this->_sheet->getHighestColumn();
        
//         // 建構Style
        
        
//         // 建構下拉選單
        
    }

    /**
     * 內容建構
     */
    public function contentBuyilder()
    {
        // 重整內容資料
        $this->rebuildContent();
        
        // 建構內容資料
        $this->_builder->addRows($this->_data);
    }
    
    /**
     * 結尾建構
     */
    public function footBuilder()
    {}
    
    /**
     * 樣式建構Style
     */
    public function styleBuilder()
    {
//         dump($this->_style);
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
     * 重整內容資料
     */
    protected function rebuildContent()
    {
        // 取得定義資料
        $content = $this->_config->content();
        $cDefined = $content['defined'];
        // 取得欄位順序
        $keySort = \nueip\helpers\ArrayHelper::gatherData($cDefined, array('key'));
        
        // 整理資料 - 依欄位設定
        $tmpData = array();
        foreach ($this->_data as $key => $row) {
            $row = (array)$row;
            
            $tmpKeySort= $keySort;
            $tmpData[$key] = array_intersect_key(array_merge($tmpKeySort, $row), $tmpKeySort);
        }
        
        // 回寫
        $this->_data = $tmpData;
        
        return $this->_data;
    }
}

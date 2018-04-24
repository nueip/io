<?php
/**
 * 參考 Attendance_record_model.php
 */


namespace app\libraries\io2\style;

/**
 * NuEIP IO Sheet Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-23
 *        
 */
class NueipStyle
{

    protected $_name = 'default';
    
    protected $_default = array();
    
    protected $_title = array();
    
    protected $_content = array();
    
    protected $_foot = array();
    
    protected $_row = array();
    
    protected $_col = array();
    
    protected $_coor = array();
    
    protected $_border = array();
    
    protected $_freeze = array();
    
    protected $_hide = array();
    
    
    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        // 初始化 - 預設樣式
        $this->initDefault();
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
     * 取得樣式 - 預設
     * @return array
     */
    public function getDefault()
    {
        return $this->_default;
    }
    
    /**
     * 取得樣式 - 預設
     * @return array
     */
    public function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * 取得樣式 - 預設
     * @return array
     */
    public function getContent()
    {
        return $this->_content;
    }
    
    /**
     * 取得樣式 - 預設
     * @return array
     */
    public function getFoot()
    {
        return $this->_foot;
    }
    
    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
    /**
     * 初始化 - 預設樣式
     */
    protected function initDefault()
    {
        $this->_default = array(
            'width' => 20,//儲存格欄寬
            'height' => 15,//儲存格欄高
            'format' => 'text',//儲存格格式-文字
            'wraptext' => true,//儲存格自動換行
            'font-name' => 'Calibri',//字體字型
            'font-size' => 11,//字體大小
            'font-bold' => false,//字體粗體
            'font-underline' => false,//字體底線
            'font-color' => 'black',//字體顏色
            'align-horizontal' => 'left',//水平對齊
            'align-vertical' => 'middle',//垂直對齊
            'border-top-style' => 'thin',//上欄線樣式
            'border-left-style' => 'thin',//左欄線樣式
            'border-right-style' => 'thin',//右欄線樣式
            'border-bottom-style' => 'thin',//下欄線樣式
            'border-top-color' => 'FFDADCDD',//上欄線顏色
            'border-left-color' => 'FFDADCDD',//左欄線顏色
            'border-right-color' => 'FFDADCDD',//右欄線顏色
            'border-bottom-color' => 'FFDADCDD',//下欄線顏色
            'background-color' => 'white',//儲存格背景顏色
        );
        
        $this->_title = array(
            'width' => 20,//儲存格欄寬
            'height' => 15,//儲存格欄高
            'wraptext' => true,//儲存格自動換行
            'font-name' => 'Calibri',//字體字型
            'font-size' => 11,//字體大小
            'font-bold' => false,//字體粗體
            'font-underline' => false,//字體底線
            'font-color' => 'black',//字體顏色
            'align-horizontal' => 'left',//水平對齊
            'align-vertical' => 'middle',//垂直對齊
            'border-top-style' => 'thin',//上欄線樣式
            'border-left-style' => 'thin',//左欄線樣式
            'border-right-style' => 'thin',//右欄線樣式
            'border-bottom-style' => 'thin',//下欄線樣式
            'border-top-color' => 'FFDADCDD',//上欄線顏色
            'border-left-color' => 'FFDADCDD',//左欄線顏色
            'border-right-color' => 'FFDADCDD',//右欄線顏色
            'border-bottom-color' => 'FFDADCDD',//下欄線顏色
            'background-color' => 'white',//儲存格背景顏色
        );
        
        $this->_content = array(
            'width' => 20,//儲存格欄寬
            'height' => 15,//儲存格欄高
            'wraptext' => true,//儲存格自動換行
            'font-name' => 'Calibri',//字體字型
            'font-size' => 11,//字體大小
            'font-bold' => false,//字體粗體
            'font-underline' => false,//字體底線
            'font-color' => 'black',//字體顏色
            'align-horizontal' => 'left',//水平對齊
            'align-vertical' => 'middle',//垂直對齊
            'border-top-style' => 'thin',//上欄線樣式
            'border-left-style' => 'thin',//左欄線樣式
            'border-right-style' => 'thin',//右欄線樣式
            'border-bottom-style' => 'thin',//下欄線樣式
            'border-top-color' => 'FFDADCDD',//上欄線顏色
            'border-left-color' => 'FFDADCDD',//左欄線顏色
            'border-right-color' => 'FFDADCDD',//右欄線顏色
            'border-bottom-color' => 'FFDADCDD',//下欄線顏色
            'background-color' => 'white',//儲存格背景顏色
            'row-odd-background-color' => 'F7F7F7',//內容奇數列背景顏色
            'row-even-background-color' => 'white'//內容偶數列背景顏色
        );
        
        $this->_foot = array(
            'width' => 20,//儲存格欄寬
            'height' => 15,//儲存格欄高
            'wraptext' => true,//儲存格自動換行
            'font-name' => 'Calibri',//字體字型
            'font-size' => 11,//字體大小
            'font-bold' => false,//字體粗體
            'font-underline' => false,//字體底線
            'font-color' => 'black',//字體顏色
            'align-horizontal' => 'left',//水平對齊
            'align-vertical' => 'middle',//垂直對齊
            'border-top-style' => 'thin',//上欄線樣式
            'border-left-style' => 'thin',//左欄線樣式
            'border-right-style' => 'thin',//右欄線樣式
            'border-bottom-style' => 'thin',//下欄線樣式
            'border-top-color' => 'FFDADCDD',//上欄線顏色
            'border-left-color' => 'FFDADCDD',//左欄線顏色
            'border-right-color' => 'FFDADCDD',//右欄線顏色
            'border-bottom-color' => 'FFDADCDD',//下欄線顏色
            'background-color' => 'white',//儲存格背景顏色
        );
    }
    
}

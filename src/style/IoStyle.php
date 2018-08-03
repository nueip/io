<?php
/**
 * 參考 Attendance_record_model.php
 */
namespace nueip\io\style;

/**
 * 樣式定義物件
 *
 * 為IO Excel樣式風格定義的物件
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-23
 *        
 */
class IoStyle
{

    /**
     * 預設參數
     *
     * @var array
     */
    protected $_options = array(
        'style' => 'io',
        'styleVersion' => '0.1'
    );

    /**
     * 樣式集 - 設定成名稱，讓格式定義的屬性class取用
     *
     * @var array $_classMap[$key] = $style;
     */
    protected $_classMap = array();

    /**
     * 凍結欄位
     *
     * 如果有凍結，會動略過上方標題列，從第一個內容開始，所以只要指定欄位
     * 不凍結(null)、欄位名稱，預設值：無
     *
     * @var array
     */
    protected $_freeze = '';

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
     *
     * @return array
     */
    public function getDefault()
    {
        return $this->_classMap['default'];
    }

    /**
     * 取得樣式 - 預設
     *
     * @return array
     */
    public function getTitle()
    {
        return $this->_classMap['title'];
    }

    /**
     * 取得樣式 - 預設
     *
     * @return array
     */
    public function getContent()
    {
        return $this->_classMap['content'];
    }

    /**
     * 取得樣式 - 預設
     *
     * @return array
     */
    public function getFoot()
    {
        return $this->_classMap['foot'];
    }

    /**
     * 取得樣式集
     *
     * @param string $className
     *            樣式集名稱
     */
    public function getClass($className)
    {
        return isset($this->_classMap[$className]) ? $this->_classMap[$className] : array();
    }

    /**
     * 取得可用樣式集清單
     */
    public function getClassList()
    {
        return array_keys($this->_classMap);
    }

    /**
     * 取得樣式 - 凍結欄位
     *
     * @return array
     */
    public function getFreeze()
    {
        return $this->_freeze;
    }

    /**
     * 設定樣式集
     *
     * @param array $class
     *            樣式集
     * @param String $className
     *            樣式集名稱
     * @return \nueip\io\style\IoStyle
     */
    public function setClass(Array $class, String $className)
    {
        $this->_classMap[$className] = $class;
        
        return $this;
    }

    /**
     * 設定樣式 - 凍結欄位
     *
     * 只需設定欄，列固定為內容的第一列
     *
     * @return array
     */
    public function setFreeze($columnCode)
    {
        $this->_freeze = strtoupper(preg_replace('/[^a-zA-Z]/', '', $columnCode));
        
        return $this;
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
    /**
     * 初始化 - 預設樣式
     * 'width' => 20.71,//儲存格欄寬
     * 'height' => -1,//儲存格欄高(-1為自動高度)
     * 'wraptext' => true,//儲存格自動換行
     * 'font-name' => '微軟正黑體',//字體字型
     * 'font-size' => 11,//字體大小
     * 'font-bold' => false,//字體粗體
     * 'font-underline' => false,//字體底線
     * 'font-color' => 'black',//字體顏色
     * 'align-horizontal' => 'left',//水平對齊
     * 'align-vertical' => 'center',//垂直對齊
     * 'border-all-style' => 'thin',//欄線樣式-全部
     * 'border-all-color' => 'FF9F9FA0',//欄線顏色-全部
     * 'border-top-style' => 'thin',//上欄線樣式
     * 'border-left-style' => 'thin',//左欄線樣式
     * 'border-right-style' => 'thin',//右欄線樣式
     * 'border-bottom-style' => 'thin',//下欄線樣式
     * 'border-outline-style' => 'thin',//外圈線樣式
     * 'border-inside-style' => 'thin',//內部線樣式
     * 'border-top-color' => 'FFDADCDD',//上欄線顏色
     * 'border-left-color' => 'FFDADCDD',//左欄線顏色
     * 'border-right-color' => 'FFDADCDD',//右欄線顏色
     * 'border-bottom-color' => 'FFDADCDD',//下欄線顏色
     * 'border-outline-color' => 'FFDADCDD',//外圈欄線顏色
     * 'border-inside-color' => 'FFDADCDD',//內部欄線顏色
     * 'background-color' => 'white'//儲存格背景顏色
     * 'row-odd-background-color' => 'F7F7F7',//內容奇數列背景顏色
     * 'row-even-background-color' => 'white'//內容偶數列背景顏色
     */
    protected function initDefault()
    {
        // 預設-全域樣式集
        $this->_classMap['default'] = array(
            'width' => 20.71, // 儲存格欄寬
            'height' => - 1, // 儲存格欄高(-1為自動高度)
            'format' => 'text', // 儲存格格式-文字
            'wraptext' => true, // 儲存格自動換行
            'font-name' => '微軟正黑體', // 字體字型
            'font-size' => 11, // 字體大小
            'font-color' => 'black', // 字體顏色
            'align-vertical' => 'center', // 垂直對齊
            'border-all-style' => 'thin', // 欄線樣式-全部
            'border-all-color' => 'FF9F9FA0' // 欄線顏色-全部
        );
        
        // 預設-標題樣式集
        $this->_classMap['title'] = array(
            // 'height' => 28.5,//儲存格欄高
            'font-size' => 12, // 字體大小
            'font-bold' => true, // 字體粗體
            'font-color' => 'white', // 字體顏色
            'align-horizontal' => 'center', // 水平對齊
            'border-all-color' => 'FFAAAAAA', // 欄線顏色-全部
            'border-outline-style' => 'thick', // 外圈欄線樣式
            'background-color' => 'FF0094D8' // 儲存格背景顏色
        );
        
        // 預設-內容樣式集
        $this->_classMap['content'] = array(
            'border-all-color' => 'FFAAAAAA', // 欄線顏色-全部
            'border-outline-style' => 'thick' // 外圈欄線樣式
                                                  // 'row-odd-background-color' => 'F7F7F7',//內容奇數列背景顏色
                                                  // 'row-even-background-color' => 'white'//內容偶數列背景顏色
        );
        
        // 預設-結尾樣式集
        $this->_classMap['foot'] = array(
            'font-color' => 'white', // 字體顏色
            'background-color' => 'FFA0A0A2', // 儲存格背景顏色
            'align-horizontal' => 'left' // 水平對齊
        );
        
        // 預設-範例樣式集
        $this->_classMap['example'] = array(
            'font-color' => 'black', // 字體顏色
            'font-bold' => false, // 字體粗體
            'background-color' => 'FFDBDCDC' // 儲存格背景顏色
        );
    }
}

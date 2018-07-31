<?php
namespace marshung\io\config;

/**
 * 簡易模式-範本
 *
 * 單一工作表版本
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-06-11
 *        
 */
class SimpleExampleConfig extends \marshung\io\config\abstracts\Config
{

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        
        // 設定檔版號
        $this->_options['version'] = '0.1';
        // 設定檔名稱
        $this->_options['configName'] = preg_replace('|Config$|', '', str_replace(array(
            __NAMESPACE__,
            '\\'
        ), '', __CLASS__));
        // 工作表名稱
        $this->_options['sheetName'] = '簡易模式-批次加保範本';
        // 模式：簡易(simple)、複雜(complex)
        $this->_options['type'] = 'simple';
        $this->_options['requiredField'] = array();
    }

    /**
     * ******************************************************
     * ************** Content Process Function **************
     * ******************************************************
     */
    
    /**
     * 內容整併處理時執行 - 迴圈內自定步驟
     *
     * @param string $key
     *            當次迴圈的Key值
     * @param array $row
     *            當次迴圈的內容
     */
    protected function eachRefactor($key, &$row)
    {}

    /**
     * **************************************************
     * ************** Map Builder Function **************
     * **************************************************
     */
    
    /**
     * 初始化對映表
     */
    protected function listMapInitialize()
    {
        // 對映表建構 - 性別 - gender
        $this->genderMapBuilder();
    }

    /**
     * 對映表建構 - 性別 - gender
     */
    protected function genderMapBuilder()
    {
        // 寫入對映表
        $this->_listMap['gender'] = array(
            array(
                'value' => '1',
                'text' => '男'
            ),
            array(
                'value' => '0',
                'text' => '女'
            )
        );
    }

    /**
     * **********************************************
     * ************** Defined Function **************
     * **********************************************
     */
    
    /**
     * 標題定義函式
     *
     * 單一標題定義可擁有單列資料，所以可定義多個標題定義
     * 簡易模式無法額外定義樣式，不建議多標題
     */
    protected function titleDefined()
    {
        // 標題1
        $title = array(
            '帳號',
            '姓名',
            '身分證字號',
            '生日',
            '性別'
        );
        
        // 設定標題定義
        $this->setTitle($title);
    }

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     * 簡易模式內容定義中的值，為資料預設值
     */
    protected function contentDefined()
    {
        // 內容
        $content = array(
            'u_no' => '',
            'c_name' => '',
            'id_no' => '',
            'birthday' => '',
            'gender' => '1'
        );
        
        // 設定內容定義
        $this->setContent($content);
    }

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    protected function footDefined()
    {
        $foot = array();
        
        // 設定結尾定義
        $this->setContent($foot);
    }
}

<?php
namespace nueip\io\config;

/**
 * 複雜模式-範本
 *
 * 單一工作表版本
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-06-11
 *        
 */
class ComplexExampleConfig extends \nueip\io\config\abstracts\Config
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
        $this->_options['sheetName'] = '非固定科目';
        // 模式：簡易(simple)、複雜(complex)
        $this->_options['type'] = 'complex';
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
     */
    protected function titleDefined()
    {
        // 標題1
        $this->_title[] = array(
            'config' => array(
                'type' => 'title',
                'name' => 'title1',
                'style' => array(
                    'font-size' => '16'
                ),
                'class' => ''
            ),
            'defined' => array(
                't1' => array(
                    'key' => 't1',
                    'value' => '帳號',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't2' => array(
                    'key' => 't2',
                    'value' => '姓名',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't3' => array(
                    'key' => 't3',
                    'value' => '身分證字號',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't4' => array(
                    'key' => 't4',
                    'value' => '生日',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't5' => array(
                    'key' => 't4',
                    'value' => '性別',
                    'col' => '2',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                )
            )
        );
        
        // 標題2
        $this->_title[] = array(
            'config' => array(
                'type' => 'title',
                'name' => 'example',
                'style' => array(),
                'class' => 'example'
            ),
            'defined' => array(
                't1' => array(
                    'key' => 't1',
                    'value' => 'A001',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't2' => array(
                    'key' => 't2',
                    'value' => '派大星',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't3' => array(
                    'key' => 't3',
                    'value' => 'ET9000001',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't4' => array(
                    'key' => 't4',
                    'value' => '2000-01-01',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't5' => array(
                    'key' => 't4',
                    'value' => '男',
                    'col' => '2',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                )
            )
        );
    }

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     */
    protected function contentDefined()
    {
        // 內容
        $this->_content = array(
            'config' => array(
                'type' => 'content',
                'name' => 'content',
                'style' => array(),
                'class' => ''
            ),
            'defined' => array(
                'u_no' => array(
                    'key' => 'u_no',
                    'value' => '帳號',
                    'col' => '1',
                    'row' => '1',
                    'style' => array('format' => 'general'),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                'c_name' => array(
                    'key' => 'c_name',
                    'value' => '姓名',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                'id_no' => array(
                    'key' => 'id_no',
                    'value' => '身分證字號',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                'birthday' => array(
                    'key' => 'birthday',
                    'value' => '生日',
                    'col' => '1',
                    'row' => '1',
                    'style' => array('format' => 'date'),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                'gender' => array(
                    'key' => 'gender',
                    'value' => '性別',
                    'col' => '2',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '1',
                    'list' => ''
                )
            )
        );
    }

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    protected function footDefined()
    {}
}

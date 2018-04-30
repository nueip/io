<?php
namespace app\libraries\io\std\abstracts;

/**
 * NuEIP IO Add Insurance Config abstract
 *
 * 未使用
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-18
 *        
 */
abstract class Config
{

    /**
     * 標題定義
     *
     * @var array
     */
    protected $_title = array();

    /**
     * 內容定義
     *
     * @var array
     */
    protected $_content = array();

    /**
     * 結尾定義
     *
     * @var array
     */
    protected $_foot = array();

    /**
     * 對映表儲存表
     * 
     * @var array
     */
    protected $_map = array();

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        // 初始化
        $this->titleDefined();
        $this->contentDefined();
        $this->footDefined();
    }

    /**
     * Destruct
     */
    public function __destruct()
    {}

    /**
     * **********************************************
     * ************** Setting Function **************
     * **********************************************
     */
    
    /**
     * 取得標題定義
     *
     * @return array
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * 取得標題定義
     *
     * @return array
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * 取得標題定義
     *
     * @return array
     */
    public function getFoot()
    {
        return $this->_foot;
    }

    /**
     * **************************************************
     * ************** Map Builder Function **************
     * **************************************************
     */
    public function contentKeyMapBuilder()
    {}

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
    {}

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     */
    protected function contentDefined()
    {}

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    protected function footDefined()
    {}
}

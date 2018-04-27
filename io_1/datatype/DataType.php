<?php

// namespace \nueip ;

// include_once('');

/**
 * NuEIP IO DataType Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-01-13
 *        
 */
class DataType
{
    /**
     * 在資料類型工作表中的順序
     * @var integer
     */
    private $_index = 0;
    
    /**
     * 資料類型名稱
     * @var unknown
     */
    private $_name = null;

    /**
     * 資料陣列
     * @var array
     */
    private $_data = array();

    /**
     * 欄位排序規則
     * @var array
     */
    private $_sort = array();

    private $_style;

    /**
     * 資料數量對映表
     * 
     * @var array
     */
    private $_dataNumMap = array();
    
    /**
     * 陣列key對映表
     * @var array
     */
    private $_keyMap = array();
    
    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->_style = new IoStyle();
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
     * 設定索引編號 - 本物件在父物件中的順序
     * @param int $index
     * @return number|Sheet
     */
    public function index($index = null)
    {
        if ($index === null) {
            return $this->_index;
        } else {
            $this->_index = $index;
            return $this;
        }
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * 設定資料
     * 
     * 在設定資料時，就要計算出影響範圍
     * 
     * @param array $data
     * @return DataType
     */
    public function setRows(array $data)
    {
        $r = sizeof($this->_data);
        
        if (!empty($this->_sort)) {
            // 有排序資料，需重整資料
            $ot = array_flip($this->_sort);
            foreach ($data as $k => $v) {
                $o = $ot;
                $this->_data[$r] = array_intersect_key(array_merge($o, $v), $o);
                
                // 計數
                $c = sizeof($o);
                $r++;
                $this->_dataNumMap[$r] = $c;
            }
        } else {
            foreach ($data as $k => $v) {
                // 加入資料暫存陣列
                $this->_data[$r] = $v;
                
                // 計數
                $c = sizeof($v);
                $r++;
                $this->_dataNumMap[$r] = $c;
            }
        }
        
        $this->_rowNum = $r;
        
        return $this;
    }

    /**
     * 取得資料
     * 
     * @return array
     */
    public function getRows()
    {
        return $this->_data;
    }

    /**
     * 設定欄位排序
     * 
     * 有欄位排序時，不在排序定義中的資料全部排除
     * 
     * @param array $sort
     * @return DataType
     */
    public function setSort(array $sort)
    {
        $this->_sort = $sort;
        
        // 重整 $this->_data
        if (!empty($this->_sort)) {
            // 有排序資料，需重整 $this->_data
            $ot = array_flip($this->_sort);
            $r = 0;
            $tmp = array();
            $this->_dataNumMap = array();
            foreach ($this->_data as $k => $v) {
                $o = $ot;
                $tmp[$k] = array_intersect_key(array_merge($o, $v), $o);
                
                // 計數
                $c = sizeof($o);
                $r++;
                $this->_dataNumMap[$r] = $c;
                
            }
            $this->_data = $tmp;
        }
        
        return $this;
    }

    /**
     * 取得欄位排序
     * @return array
     */
    public function getSort()
    {
        return $this->_sort;
    }

    public function setStyles(IoStyle $style)
    {
        $this->_style = $style;
        return $this;
    }

    public function getStyles()
    {
        return $this->_style;
    }

    /**
     * 取得資料數量對映表
     * @return array
     */
    public function getDataNumMap()
    {
        return $this->_dataNumMap;
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    private function _setStyle()
    {}

//     public function getStyle()
//     {
//         $this->_style['col']['s_sn'] = array('width' => 14.71);
//         $this->_style['col'][2] = array('width' => 14.71);
//        
//         $this->_style['row'][1] = array(
//             'height' => 28.5,
//             'align-horizontal' => 'middle'
//         );
//        
//         // 回傳$this->tableStyle
//         return $this->_style;
//     }
}

<?php

// namespace \nueip ;

// include_once('');

/**
 * NuEIP IO Sheet Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-01-13
 *        
 */
class IoStyle
{

    private $_name = 'default';
    
    private $_default = array();
    
    private $_row = array();
    
    private $_col = array();
    
    private $_coor = array();
    
    private $_border = array();
    
    private $_list = array();
    
    private $_merge = array();
    
    private $_freeze = array();
    
    private $_filter = array();
    
    private $_lock = array();
    
    private $_hide = array();
    
    
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
     * 設定樣式 - 預設
     * @param array $style 樣式
     * @return Style
     */
    public function setDefault(array $style)
    {
        $this->_default = $style;
        return $this;
    }
    
    /**
     * 取得樣式 - 預設
     * @return array
     */
    public function getDefault()
    {
        return $this->_default;
    }
    
    /**
     * 設定樣式 - 列
     * 
     * @param array $style 樣式
     * @param int $row 列
     * @return Style
     */
    public function setRow(array $style, $row = null)
    {
        if ($row === null) {
            $this->_row = $style;
        } else {
            $this->_row[$row] = $style;
        }
        
        return $this;
    }
    
    /**
     * 取得樣式 - 列
     * 
     * @param int $row 列
     * @return array
     */
    public function getRow($row = null)
    {
        $opt = array();
        
        if ($row === null) {
            $opt = $this->_row;
        } else {
            $opt = isset($this->_row[$row]) ? $this->_row[$row] : array();
        }
        
        return $opt;
    }
    
    /**
     * 設定樣式 - 欄
     * 
     * @param array $style 樣式
     * @param string | int $col 欄
     * @return Style
     */
    public function setCol(array $style, $col = null)
    {
        if ($col === null) {
            $this->_col = $style;
        } else {
            $this->_col[$col] = $style;
        }
        
        return $this;
    }
    
    /**
     * 取得樣式 - 欄
     * 
     * @param string | int $col 欄
     * @return array
     */
    public function getCol($col = null)
    {
        $opt = array();
        
        if ($col=== null) {
            $opt = $this->_col;
        } else {
            $opt = isset($this->_col[$col]) ? $this->_col[$col] : array();
        }
        
        return $opt;
    }
    
    /**
     * 設定樣式 - 座標
     * 
     * @param array $style 樣式
     * @param int $row 列
     * @param string | int $col 欄
     * @return Style
     */
    public function setCoor(array $style, $row = null, $col = null)
    {
        if ($row === null && $col === null) {
            $this->_coor = $style;
        } elseif($row !== null && $col !== null) {
            $this->_coor[$row][$col] = $style;
        }
        
        return $this;
    }
    
    /**
     * 取得樣式 - 座標
     * @param int $row 列
     * @param string | int $col 欄
     * @return array
     */
    public function getCoor($row = null, $col = null)
    {
        $opt = array();
        
        if ($row === null && $col === null) {
            $opt = $this->_coor;
        } elseif($row !== null && $col !== null) {
            $opt = isset($this->_coor[$row][$col]) ? $this->_coor[$row][$col] : array();
        }
        
        return $opt;
    }
    
    /**
     * 設定 資料邊界 border
     */
    public function setBorder(string $type, array $borderStyle)
    {
        if (!isset($this->_style['border'])) {
            $this->_style['border'] = array();
        }
        
        $tp = array('all', 'left', 'right', 'top', 'bottom');
        if (in_array($type, $tp) && !empty($borderStyle)) {
            $this->_style['border'][$type] = $borderStyle;
        }
        
        return $this;
    }
    
    /**
     * 設定 下拉選單 list
     */
    public function setList(array $coor, array $list)
    {
        if (!isset($this->_style['list'])) {
            $this->_style['list'] = array();
        }
        
        if (empty($list)) {
            return $this;
        }
        
        $sf = array_flip($this->_sort);
        
        if (isset($coor['row']) && isset($coor['col'])) {
            // 座標
            
        } elseif (isset($coor['row'])) {
            // 列
        } elseif (isset($coor['col'])) {
            // 欄
        }
        
        
        
        
        
        
        if (isset($sf[$key])) {
            $this->_style['list'][$key] = $list;
        }
        
        return $this;
    }
    
    /**
     * 設定 欄位合併 merge - 會影響資料放入位置，必預先處理
     */
    public function setMerge(string $key, array $merge)
    {
        if (!isset($this->_style['merge'])) {
            $this->_style['merge'] = array();
        }
        
        $sf = array_flip($this->_sort);
        if (isset($sf[$key]) && !empty($merge)) {
            $this->_style['merge'][$key] = $merge;
        }
        
        return $this;
    }
    
    /**
     * 設定 欄位凍結 freeze
     */
    public function setFreeze(array $freeze)
    {
        if (!isset($this->_style['freeze'])) {
            $this->_style['freeze'] = array();
        }
        
        $this->_style['freeze'] = $freeze;
        
        return $this;
    }
    
    /**
     * 設定 欄位篩選 filter
     */
    public function setFilter(array $filter)
    {
        if (!isset($this->_style['filter'])) {
            $this->_style['filter'] = array();
        }
        
        $sf = array_flip($this->_sort);
        foreach ($filter as $key => $col) {
            
        }
        if (isset($sf[$key]) && !empty($filter)) {
            $this->_style['merge'][$key] = $filter;
        }
        
        return $this;
    }
    
    /**
     * 設定 鎖定 lock
     */
    public function setLock()
    {}
    
    /**
     * 設定 列/欄隱藏 hide
     */
    public function setHide()
    {}
    

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
    /**
     * 初始化 - 預設樣式
     */
    private function initDefault()
    {
        $this->_default = array(
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
    }
    
}

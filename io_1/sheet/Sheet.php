<?php

// namespace \nueip ;

// include_once('');

/**
 * NuEIP IO Sheet Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-01-13
 *        
 */
class Sheet
{
    /**
     * 工作表的順序
     * @var integer
     */
    private $_index = 0;
    
    private $_name = 'Worksheet';

    private $_visible = true;

    private $_lock = false;

    private $_dataType = array();

    private $_actDataType = null;

    private $_dataTypeNameMap = array();

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        // 初始化
        $this->setDataType(1);
    }

    /**
     * Destruct
     */
    public function __destruct()
    {}

    /**
     * ********************************************
     * ************** Sheet Function **************
     * ********************************************
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

    public function hide()
    {
        $this->_visible = false;
        return $this;
    }

    public function show()
    {
        $this->_visible = true;
        return $this;
    }

    public function lock()
    {
        $this->_lock = true;
        return $this;
    }

    public function unlock()
    {
        $this->_lock = false;
        return $this;
    }

    /**
     * 取得資料類型所在的索引位置
     *
     * @param DataType $dataType            
     * @return number | false
     */
    public function indexOf(DataType $dataType)
    {
        $index = array_search($dataType, $this->_dataType);
        return $index === false ? $index : $index + 1;
    }

    /**
     * ***********************************************
     * ************** DataType Function **************
     * ***********************************************
     */
    
    /**
     * 將第n個資料類型設定為active
     *
     * @param int $num            
     */
    public function setDataType($num = 1)
    {
        $this->_fillChild($num);
        
        if (isset($this->_dataType[$num - 1])) {
            $this->_actDataType = &$this->_dataType[$num - 1];
        } else {
            throw new Exception('資料類型不存在!', 7001);
        }
        
        return $this;
    }

    /**
     * 取得第n個子元素 - 資料類型
     * 
     * @param int $num            
     */
    public function getDataType($num = null)
    {
        if (!empty($num)) {
            $this->setDataType($num);
            return $this->_dataType[$num];
        }
        
        return $this->_dataType;
    }

    /**
     * 取得生效中的子元素 - 資料類型
     */
    public function dataType($num = null)
    {
        if (! empty($num)) {
            $this->setDataType($num);
        }
        
        return $this->_actDataType;
    }

    /**
     * 取得datatype數量
     */
    public function getDataTypeNumber()
    {
        return sizeof($this->_dataType);
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
    /**
     * 補足子元素
     *
     * 因excel的工作表是有序不可中斷的，所以要補足不夠的工作表
     *
     * @param int $num            
     */
    private function _fillChild($num = 1)
    {
        if ($num > sizeof($this->_dataType)) {
            for ($i = sizeof($this->_dataType); $i < $num; $i ++) {
                $this->_dataType[] = $this->_childInit($i);
            }
        }
        return $this;
    }

    /**
     * 子元素初始化
     *
     * @return DataType
     */
    private function _childInit($i)
    {
        $dataType = new DataType();
        $dataType->setName($i+1);
        $dataType->index($i);
        
        return $dataType;
    }
}

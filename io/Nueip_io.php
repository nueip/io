<?php

// namespace nueip ;

// include_once('');
include_once (APPPATH . '/libraries/io/builder/ExcelBuilder.php');
include_once (APPPATH . '/libraries/io/sheet/Sheet.php');
include_once (APPPATH . '/libraries/io/datatype/DataType.php');
include_once (APPPATH . '/libraries/io/style/IoStyle.php');

/**
 * NuEIP IO Library
 * 
 * test:
 * // 測試函式
 * $this->test();
 * 
 * 問題:
 * 1.未支援namespace
 * 2.未支援composer
 * 3.未支援autoload
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-01-13
 *        
 */
class Nueip_io
{
    private $_builder = null;

    private $_sheet = array();

    private $_actSheet = null;

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        // 初始化
        $this->setBuilder('excel');
        $this->setSheet(1);
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
    public function setBuilder($builder = null)
    {
        switch ($builder) {
            default:
            case 'excel':
                $this->_builder = new \ExcelBuilder();
                break;
        }
        
        return $this;
    }
    
    public function getBuilder()
    {
        return $this->builder();
    }
    
    public function builder()
    {
        $this->_builder->sheet($this->_sheet);
        return $this->_builder;
    }

    /**
     * 將第n個工作表設定為active
     *
     * @param int $num            
     */
    public function setSheet($num = 1)
    {
        $this->_fillChild($num);
        
        if (isset($this->_sheet[$num - 1])) {
            $this->_actSheet = &$this->_sheet[$num - 1];
        } else {
            throw new Exception('工作表不存在!', 6001);
        }
        
        return $this;
    }

    /**
     * 取得第n個子元素 - 工作表
     * 
     * @param int $num            
     */
    public function getSheet($num)
    {
        $this->setSheet($num);
        return $this->_sheet[$num];
    }

    /**
     * 取得生效中的子元素 - 工作表
     */
    public function sheet($num = null)
    {
        if (! empty($num)) {
            $this->setSheet($num);
        }
        
        return $this->_actSheet;
    }

    /**
     * 取得子元素數量 - 工作表
     */
    public function getSheetNumber()
    {
        return sizeof($this->_sheet);
    }

    /**
     * ***********************************************
     * ************** Building Function **************
     * ***********************************************
     */
    
    
    
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
        if ($num > sizeof($this->_sheet)) {
            for ($i = sizeof($this->_sheet); $i < $num; $i ++) {
                $this->_sheet[] = $this->_childInit($i);
            }
        }
        return $this;
    }

    /**
     * 子元素初始化
     *
     * @return Sheet
     */
    private function _childInit($i)
    {
        $sheet = new Sheet($i);
        $sheet->index($i);
        
        return $sheet;
    }
    
    /**
     * 測試函式
     */
    public function test() {
        $this->load->library('tw_ins_management/Tw_ins_management_component');
        
        // 取得 員工保險清單
        $data = $this->tw_ins_management_component->empInsList(70);
        $data = $data['aData'];
        foreach ($data as $k => &$v) {
            unset($v['content2']);
        }
        
        $sort = array(
            's_sn',
            'c_sn',
            'u_sn',
            'uf_sn',
            'start_date',
            'actionType',
            'content',
        );
        
        include_once(APPPATH.'/libraries/io/Nueip_io.php');
        
        $io = new Nueip_io();
        
        //==================
        $sh = $io->sheet();
        
        $dt = $sh->dataType(1);
        
        $dt->setRows(array(
            array(
                's_sn' => 'PK',
                'c_sn' => '公司',
                'u_sn' => '員工',
                'uf_sn' => '眷屬',
                'start_date' => '時間',
                'actionType' => '類型',
                'content' => '內容',
            ),
        ));
        
        $dt->setSort($sort);
        
        
        $dt = $sh->dataType(2);
        
        $dt->setRows($data);
        
        $dt->setSort($sort);
        
        var_export($data);
        
//         var_export($dt->getRows());

//         exit;
        
        
        //==================
        $sh = $io->sheet(2);
        
        $dt = $sh->dataType();
        
        $dt->setRows($data);
        
        
        
        $bu = $io->builder();
        
        $bu->build();
        
//         $bu->output();

//         var_export($dt->getRows());
//         var_export($dt->getSort());

//         $dt2 = $sh->dataType(2)->setRows('test2');

// //         $dt1->getRows();
// //         $dt2->getRows();

//         echo $sh->indexOf($dt2);
//         echo "\n";
//         echo $sh->indexOf('');
//         echo "\n";
//         echo $sh->indexOf($dt1);
//         echo "\n";
        
        
        exit;
        
        $act1 = $io->sheet()->dataType();
        $act2 = $io->sheet()->dataType();
        
        $io->sheet()->dataType()->setRows('test')->getRows();
        
        $act1->getRows();
        $act2->getRows();
        
        $act1->setRows('$act1')->getRows();
        $act2->getRows();
        
    }
}

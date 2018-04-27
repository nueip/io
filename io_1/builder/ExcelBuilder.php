<?php

// namespace \nueip ;

// include_once('');

/**
 * NuEIP IO Builder Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-01-13
 *        
 */
class ExcelBuilder
{

    private $_name = 'Excel';

    private $_builder = null;

    /**
     * 使用中的工作表物件
     *
     * @var object
     */
    private $_sheet = null;

    /**
     * 記錄最後使用到的座標 - col
     *
     * @var integer
     */
    private $_col = 0;

    /**
     * 記錄最後使用到的座標 - row
     *
     * @var integer
     */
    private $_row = 0;

    /**
     * 取座標時的輸出格式
     *
     * @var string number/excel
     */
    private $_coordinateType = 'number';

    /**
     * 記錄被合併掉的欄位座標
     *
     * @var array
     */
    private $_noExists = array();

    /**
     * 資料影響範圍 - 資料計數
     *
     * @var array $_dataNumMap[$sh][$dt][$row] = $col;
     */
    private $_dataNumMap = array();
    
    /**
     * 資料影響範圍 - 資料欄位座標 - 處理中的資料
     *
     * @var array
     */
    private $_dataRangeMap = array();
    
    /**
     * 資料影響範圍 - 資料欄位座標 - 處理中的工作表
     *
     * @var array 
     */
    private $_sheetRangeMap = array();
    
    

    /**
     * Builder - Cache for Active Sheet
     *
     * @var Sheet
     */
    private $_sh = null;

    /**
     * 記錄已使用的工作表名稱 - 唯一
     * 
     * @var array
     */
    private $_shName = array();

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->_builder = new \PHPExcel();
        // $this->_builder = \PHPExcelHelper::newExcel();
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
    public function getName()
    {
        return $this->_name;
    }

    public function output($filename = 'excel', $excelType = 'Excel2007')
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = \PHPExcel_IOFactory::createWriter($this->_builder, $excelType);
        $objWriter->save('php://output');
        exit();
    }

    public function file()
    {}

    public function package()
    {}

    public function sheet($sheet)
    {
        $this->_sheet = $sheet;
        
        return $this;
    }

    public function build()
    {
        // Sheets Process
        if (is_array($this->_sheet)) {
            foreach ($this->_sheet as $k => $sh) {
                // 工作表開始時要處理的事
                $this->_sheetBegin();
                
                if ($k > 0) {
                    // 建立工作表
                    $this->_sh = $this->_builder->createSheet();
                } else {
                    // 取得工作表
                    $this->_sh = $this->_builder->setActiveSheetIndex($k);
                }
                // Sheet Builder
                $this->_sheetBuilder($sh);
            }
        }
    }

    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    private function _sheetBuilder($sh)
    {
        // 設定工作表名稱
        $this->_sheetTitle($sh->getName());
        
        // 設定可見性
        
        // 設定鎖定
        
        // 處理資料內容
        $dts = $sh->getDataType();
        if (is_array($dts)) {
            foreach ($dts as $k => $dt) {
                // 資料類型開始時要處理的事
                $this->_dataTypeBegin();
                
                // 計算資料範圍
                $this->_dataRangeHandle($dt);
                
                // 設定樣式 - 因欄位合併會影響資料放置，所以樣式先設定
                $this->_styleBuilder($dt);
                
                // 處理資料內容
                $this->_dataTypeBuilder($dt);
            }
        }
    }
    
    /**
     * 建構 - 樣式處理
     * 
     * @param Style $dt
     */
    private function _styleBuilder(DataType $dt)
    {
        $style = $dt->getStyles();
        
        // 建構預設樣式
        $defStyle = $style->getDefault();
        // 樣式建構 - 預設樣式
        $this->_styleBuilder4Default($defStyle);
        
        
//         var_export($def);
//         exit;
    }
    
    /**
     * 建構 - 資料處理
     * 
     * @param DataType $dt
     */
    private function _dataTypeBuilder(DataType $dt)
    {
        // 資料放置
        $data = $dt->getRows();
        $this->_setCoorType('excel');
        foreach ($data as $rk => $rd) {
            $this->_newRow();
            foreach ($rd as $ck => $cd) {
                $this->_newCol();
                $coor = $this->_coordinate();
                $this->_sh->setCellValue($coor, $cd);
            }
        }
        
        // var_export($data);
    }
    
    /** 
     * 計算資料範圍
     * 
     * @param DataType $dt
     */
    private function _dataRangeHandle(DataType $dt)
    {
        $numMap = $dt->getDataNumMap();
        
        $sr = sizeof($this->_sheetRangeMap);
        
        foreach ($numMap as $k => $v) {
            $this->_dataRangeMap[] = array('row' => ($sr+$k), 'col' => $v);
            $this->_sheetRangeMap[] = array('row' => ($sr+$k), 'col' => $v);
        }
        
        
//         echo '$this->_dataRangeMap = ';
//         var_export($this->_dataRangeMap);
//         echo "\n";
//         echo '$this->_sheetRangeMap = ';
//         var_export($this->_sheetRangeMap);
//         echo "\n";
        
        
        //$_dataRangeMap
    }
    
    /**
     * 工作表名稱處理
     *
     * @param unknown $sheetTitle
     * @return ExcelBuilder
     */
    private function _sheetTitle($sheetTitle)
    {
        // Sheet Title處理：1.Excel的SheetTitle最多31個字 2.移除不可用於Sheet Title的七個字元 \ / * [ ] : ?
        $sheetTitle = mb_substr(preg_replace('|[\\\/\*\]\[\:\?]|', '', $sheetTitle), 0, 31);
        // 檢查是否使用過
        if (isset($this->_shName[$sheetTitle])) {
            // 用過了，不處理
        } else {
            // 沒用過
            $this->_sh->setTitle($sheetTitle);
            $this->_shName[$sheetTitle] = '';
        }
        
        return $this;
    }
    
    /**
     * 工作表開始時要處理的事
     */
    private function _sheetBegin()
    {
        // 初始化 - 座標
        $this->_coorInit();
        // 初始化 - 資料影響範圍 - 資料欄位座標 - 處理中的工作表
        $this->_sheetRangeMap = array();
    }
    
    /**
     * 資料類型開始時要處理的事
     */
    private function _dataTypeBegin()
    {
        // 初始化 - 資料影響範圍 - 資料欄位座標 - 處理中的資料
        $this->_dataRangeMap = array();
    }
    
    
    /**
     * *************************************************
     * ************** Coordinate Function **************
     * *************************************************
     */
    
    /**
     * 座標初始化
     */
    private function _coorInit()
    {
        $this->_row = 0;
        $this->_col = 0;
        return $this;
    }

    /**
     * 下一列
     */
    private function _newRow()
    {
        $this->_row ++;
        $this->_col = 0;
        return $this;
    }

    /**
     * 下一欄
     */
    private function _newCol()
    {
        $this->_col ++;
        return $this;
    }

    /**
     * 取得目前座標
     * 
     * @param string $type            
     */
    private function _coordinate($type = null)
    {
        if (! empty($type)) {
            $this->_setCoorType($type);
        }
        
        $type = $this->_getCoorType();
        
        $coor = $this->_col . ',' . $this->_row;
        switch ($type) {
            case 'number':
                // 直接輸出
                break;
            case 'excel':
                // 格式轉換
                $coor = $this->_coorNum2Excel($coor);
                break;
        }
        return $coor;
    }

    /**
     * 座標轉換 - 數字座標=>Excel座標
     */
    private function _coorNum2Excel($coor)
    {
        list ($c, $r) = explode(',', $coor);
        $c = $c >= 1 ? $c - 1 : 0;
        // PHPExcel轉換函式 0=>A, 1=>B ...
        $c = \PHPExcel_Cell::stringFromColumnIndex($c);
        
        return $c . $r;
    }

    /**
     * 座標轉換 - Excel座標=>數字座標
     */
    private function _coorExcel2Num($coor)
    {
        preg_match('|^([A-Z]+)([0-9]+)|', $coor, $matches);
        $c = $matches[1];
        $r = $matches[2];
        // PHPExcel轉換函式 A=>1, B=>2 ...
        $c = \PHPExcel_Cell::columnIndexFromString($c);
        
        return $c . ',' . $r;
    }

    private function setCoor()
    {}

    /**
     * 設定取座標時的預設輸出格式
     *
     * @param string $type            
     * @return ExcelBuilder
     */
    private function _setCoorType($type)
    {
        if (in_array($type, array(
            'number',
            'excel'
        ))) {
            $this->_coordinateType = $type;
        }
        return $this;
    }
    
    /**
     * 取得座標時的預設輸出格式
     * @return string
     */
    private function _getCoorType()
    {
        return $this->_coordinateType;
    }
    
    private function _getCoorRange4DataType()
    {
        $s = current($this->_dataRangeMap);
        $e = end($this->_dataRangeMap);
        
        $sc = $this->_coorNum2Excel('1,'.$s['row']);
        $ec = $this->_coorNum2Excel($e['col'].','.$e['row']);
        
        return $sc.':'.$ec;
    }
    /**
     * *************************************************
     * ************** Coordinate Function **************
     * *************************************************
     */
    
    /**
     * 樣式建構 - 預設樣式
     * @param array $style
     */
    private function _styleBuilder4Default(array $style)
    {
        $range = $this->_getCoorRange4DataType();
        $styleArray = style_func();
        
        $this->_sh->getStyle($range)->applyFromArray($styleArray['thin']);
        
        
        
        
//         foreach () {
            
//         }
    }
}

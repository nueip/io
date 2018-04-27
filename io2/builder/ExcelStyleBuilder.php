<?php
namespace app\libraries\io2\builder;

/**
 * NuEIP IO Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class ExcelStyleBuilder
{

    /**
     * 輸出暫存資料
     *
     * @var object
     */
    protected static $_builder;

    /**
     * 工作表暫存資料
     *
     * @var array
     */
    protected static $_sheet;

    protected static $_styleMap = array(
        'excelColorMap' => array(
            'black' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK,
            'blue' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE,
            'darkblue' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE,
            'darkgreen' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN,
            'darkred' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKRED,
            'darkyellow' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKYELLOW,
            'green' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN,
            'red' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED,
            'white' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE,
            'yellow' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW
        ),
        'excelHorizontalAlignMap' => array(
            'center' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'center_continuous' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            'general' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,
            'justify' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY,
            'left' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'right' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
        ),
        'excelVerticalAlignMap' => array(
            'bottom' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
            'center' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'middle' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'justify' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY,
            'top' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
        ),
        'excelBorderMap' => array(
            'dashdot' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOT,
            'dashdotdot' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOTDOT,
            'dashed' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHED,
            'dotted' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
            'double' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
            'hair' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
            'medium' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'mediumdashdot' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOT,
            'mediumdashdotdot' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOTDOT,
            'mediumdashed' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHED,
            'none' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
            'slantdashdot' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_SLANTDASHDOT,
            'thick' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            'thin' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ),
        'excelFormatMap' => array(
            'txt' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
            'text' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
            'number' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER,
            'number_00' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00,
        )
    );
    
    /**
     * *********************************************
     * ************** Public function **************
     * *********************************************
     */
    
    /**
     * 建立Excel樣式 - 預設值
     *
     * @author Peter.Chiu
     * @author Mars.Hung <tfaredxj@gmail.com> (Refactoring)
     * 
     * @param array $style 樣式集
     * @param object $spreadsheet Excel物件
     * @return $sheet Excel物件
     */
    public static function setExcelDefaultStyle(Array $style, &$spreadsheet)
    {
        // 取得工作表
        $sheet = $spreadsheet->getActiveSheet();
        
        foreach ($style as $key => $value) {
            switch ($key) {
                case 'width':
                    // 寬度
                    $sheet->getDefaultColumnDimension()->setWidth($value);
                    break;
                case 'height':
                    // 高度
                    $sheet->getDefaultRowDimension()->setRowHeight($value);
                    break;
                case 'format':
                    // 儲存格格式
                    $format = self::excelFormatMap($value);
                    $spreadsheet->getDefaultStyle()->getNumberFormat()->setFormatCode($format);
                    break;
                case 'wraptext':
                    // 自動換行
                    $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText($value);
                    break;
                case 'font-name':
                    // 字型
                    $spreadsheet->getDefaultStyle()->getFont()->setName($value);
                    break;
                case 'font-size':
                    // 字體大小
                    $spreadsheet->getDefaultStyle()->getFont()->setSize($value);
                    break;
                case 'font-bold':
                    // 粗體
                    $spreadsheet->getDefaultStyle()->getFont()->setBold($value);
                    break;
                case 'font-underline':
                    // 底線
                    $spreadsheet->getDefaultStyle()->getFont()->setUnderline($value);
                    break;
                case 'font-color':
                    // 字體顏色
                    $fontColor = self::excelColorMap($value);
                    $spreadsheet->getDefaultStyle()->getFont()->getColor()->setARGB($fontColor);
                    break;
                case 'align-horizontal':
                    // 水平對齊
                    $horiAlign = self::excelHorizontalAlignMap($value);
                    $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal($horiAlign);
                    break;
                case 'align-vertical':
                    // 垂直對齊
                    $veriAlign = self::excelVerticalAlignMap($value);
                    $spreadsheet->getDefaultStyle()->getAlignment()->setVertical($veriAlign);
                    break;
                case 'border-all-style':
                    // 範圍內全部框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $spreadsheet->getDefaultStyle()->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => $borderStyle,
                            ],
                        ],
                    ]);
                    break;
                case 'border-top-style':
                    // 範圍內上框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getTop()->setBorderStyle($borderStyle);
                    break;
                case 'border-bottom-style':
                    // 範圍內下框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getBottom()->setBorderStyle($borderStyle);
                    break;
                case 'border-left-style':
                    // 範圍內左框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getLeft()->setBorderStyle($borderStyle);
                    break;
                case 'border-right-style':
                    // 範圍內右框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getRight()->setBorderStyle($borderStyle);
                    break;
                case 'border-all-color':
                    // 範圍內全部框顏色
                    $borderColor = self::excelColorMap($value);
                    $spreadsheet->getDefaultStyle()->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'color' => ['argb' => $borderColor],
                            ],
                        ],
                    ]);
                    break;
                case 'border-top-color':
                    // 範圍內上框顏色
                    $borderColor = self::excelColorMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getTop()->getColor()->setARGB($borderColor);
                    break;
                case 'border-bottom-color':
                    // 範圍內下框顏色
                    $borderColor = self::excelColorMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getBottom()->getColor()->setARGB($borderColor);
                    break;
                case 'border-left-color':
                    // 範圍內左框顏色
                    $borderColor = self::excelColorMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getLeft()->getColor()->setARGB($borderColor);
                    break;
                case 'border-right-color':
                    // 範圍內右框顏色
                    $borderColor = self::excelColorMap($value);
                    $spreadsheet->getDefaultStyle()->getBorders()->getRight()->getColor()->setARGB($borderColor);
                    break;
                case 'background-color':
                    // 背景顏色
                    $backgroundColor = self::excelColorMap($value);
                    $spreadsheet->getDefaultStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $spreadsheet->getDefaultStyle()->getFill()->getStartColor()->setARGB($backgroundColor);
                    break;
            }
        }
        
        return new static();
    }
    
    /**
     * 建立Excel樣式
     *
     * @author Peter.Chiu
     * @author Mars.Hung <tfaredxj@gmail.com> (Refactoring)
     *
     * @param array $style 樣式集
     * @param object $spreadsheet Excel物件
     * @param string $cellRange 樣式的欄位範圍
     * @param int $rowIndex 列數
     * @return $sheet Excel物件
     */
    public static function setExcelRangeStyle(Array $style, &$spreadsheet, $cellRange)
    {
        if (empty($style)) {
            // 沒有樣式資料，不處理
            return new static();
        }
        
        // === 解析 - 欄位座標範圍 ===
        $crp = self::cellRangeParser($cellRange);
        if (empty($crp)) {
            // 欄位範圍格式誤，不處理
            return new static();
        }
        
        // 取得欄位-起訖
        $cellStart = $crp[1];
        $cellEnd = $crp[3];
        
        // 取得列-起訖
        $rowStart = $crp[2];
        $rowEnd = $crp[4];
        
        // 取得工作表
        $sheet = $spreadsheet->getActiveSheet();
        
        // 遍歷樣式集 - 處理樣式設定
        foreach ($style as $key => $value) {
            switch ($key) {
                case 'width':
                    // 寬度
                    // 遍歷欄位處理寬度 - 需做文字<=>位置轉換
                    for ($i = \yidas\phpSpreadsheet\Helper::alpha2num($cellStart); $i <= \yidas\phpSpreadsheet\Helper::alpha2num($cellEnd); $i++) {
                        $dim = \yidas\phpSpreadsheet\Helper::num2alpha($i);
                        $sheet->getColumnDimension($dim)->setWidth($value);
                    }
                    break;
                case 'height':
                    // 高度
                    // 遍歷處理列高
                    for ($i = $rowStart; $i <= $rowEnd; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight($value);
                    }
                    break;
                case 'format':
                    // 儲存格格式
                    $format = self::excelFormatMap($value);
                    $sheet->getStyle($cellRange)->getNumberFormat()->setFormatCode($format);
                    break;
                case 'wraptext':
                    // 自動換行
                    $sheet->getStyle($cellRange)->getAlignment()->setWrapText($value);
                    break;
                case 'font-name':
                    // 字型
                    $sheet->getStyle($cellRange)->getFont()->setName($value);
                    break;
                case 'font-size':
                    // 字體大小
                    $sheet->getStyle($cellRange)->getFont()->setSize($value);
                    break;
                case 'font-bold':
                    // 粗體
                    $sheet->getStyle($cellRange)->getFont()->setBold($value);
                    break;
                case 'font-underline':
                    // 底線
                    $sheet->getStyle($cellRange)->getFont()->setUnderline($value);
                    break;
                case 'font-color':
                    // 字體顏色
                    $fontColor = self::excelColorMap($value);
                    $sheet->getStyle($cellRange)->getFont()->getColor()->setARGB($fontColor);
                    break;
                case 'align-horizontal':
                    // 水平對齊
                    $horiAlign = self::excelHorizontalAlignMap($value);
                    $sheet->getStyle($cellRange)->getAlignment()->setHorizontal($horiAlign);
                    break;
                case 'align-vertical':
                    // 垂直對齊
                    $veriAlign = self::excelVerticalAlignMap($value);
                    $sheet->getStyle($cellRange)->getAlignment()->setVertical($veriAlign);
                    break;
                case 'border-all-style':
                    // 範圍內全部框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle($borderStyle);
                    break;
                case 'border-top-style':
                    // 範圍內上框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getTop()->setBorderStyle($borderStyle);
                    break;
                case 'border-bottom-style':
                    // 範圍內下框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getBottom()->setBorderStyle($borderStyle);
                    break;
                case 'border-left-style':
                    // 範圍內左框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getLeft()->setBorderStyle($borderStyle);
                    break;
                case 'border-right-style':
                    // 範圍內右框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getRight()->setBorderStyle($borderStyle);
                    break;
                case 'border-outline-style':
                    // 範圍內右框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getOutline()->setBorderStyle($borderStyle);
                    break;
                case 'border-inside-style':
                    // 範圍內右框樣式
                    $borderStyle = self::excelBorderMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getInside()->setBorderStyle($borderStyle);
                    break;
                case 'border-all-color':
                    // 範圍內全部框顏色
                    $borderColor = self::excelColorMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->getColor()->setARGB($borderColor);
                    break;
                case 'border-top-color':
                    // 範圍內上框顏色
                    $borderColor = self::excelColorMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getTop()->getColor()->setARGB($borderColor);
                    break;
                case 'border-bottom-color':
                    // 範圍內下框顏色
                    $borderColor = self::excelColorMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getBottom()->getColor()->setARGB($borderColor);
                    break;
                case 'border-left-color':
                    // 範圍內左框顏色
                    $borderColor = self::excelColorMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getLeft()->getColor()->setARGB($borderColor);
                    break;
                case 'border-right-color':
                    // 範圍內右框顏色
                    $borderColor = self::excelColorMap($value);
                    $sheet->getStyle($cellRange)->getBorders()->getRight()->getColor()->setARGB($borderColor);
                    break;
                case 'background-color':
                    // 背景顏色
                    $backgroundColor = self::excelColorMap($value);
                    $sheet->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle($cellRange)->getFill()->getStartColor()->setARGB($backgroundColor);
                    break;
            }
        }
        
        return new static();
    }
    
    /**
     * 凍結欄位
     * 
     * @param string $freezeCell
     * @param object $spreadsheet
     */
    public static function setFreeze($freezeCell, &$spreadsheet)
    {
        // 取得工作表
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->freezePane($freezeCell);
    }
    
    
    public static function listBuilder()
    {}
    
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
     * 解析 - 欄位座標範圍
     * 
     * @param string $cellRange 欄位座標範圍 cell range
     */
    protected static function cellRangeParser($cellRange)
    {
        preg_match('/([A-Z]+)([0-9]+)\:([A-Z]+)([0-9]+)/', $cellRange, $matches);
        
        return $matches;
    }
    
    /**
     * ******************************************
     * ************** Map Function **************
     * ******************************************
     */

    /**
     * 轉換字串為excel顏色樣式
     *
     * @author Peter.Chiu
     * @author Mars.Hung <tfaredxj@gmail.com> (Refactoring)
     * 
     * @param string $color
     *            顏色文字
     * @return string $excelColor excel顏色樣式
     */
    public static function excelColorMap($color)
    {
        $colorMap = self::$_styleMap['excelColorMap'];
        return isset($colorMap[$color]) ? $colorMap[$color] : $color;
    }

    /**
     * 轉換字串為excel水平對準方式
     *
     *
     * @author Peter.Chiu
     * @author Mars.Hung <tfaredxj@gmail.com> (Refactoring)
     *        
     * @param string $alignment
     *            對準方式文字
     * @return string $excelAlignment excel對準方式
     */
    public static function excelHorizontalAlignMap($alignment)
    {
        $alignmentMap = self::$_styleMap['excelHorizontalAlignMap'];
        return isset($alignmentMap[$alignment]) ? $alignmentMap[$alignment] : \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
    }

    /**
     * 轉換字串為excel重直對準方式
     *
     *
     * @author Peter.Chiu
     * @author Mars.Hung <tfaredxj@gmail.com> (Refactoring)
     *        
     * @param string $alignment
     *            對準方式文字
     * @return string $excelAlignment excel對準方式
     */
    public static function excelVerticalAlignMap($alignment)
    {
        $alignmentMap = self::$_styleMap['excelVerticalAlignMap'];
        return isset($alignmentMap[$alignment]) ? $alignmentMap[$alignment] : \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    }
    
    /**
     * 轉換字串為excel框線樣式
     *
     *
     * @author Peter.Chiu
     * @author Mars.Hung <tfaredxj@gmail.com> (Refactoring)
     *
     * @param string $border
     *            框線樣式文字
     * @return string $excelBorder excel框線樣式
     */
    public static function excelBorderMap($border)
    {
        $borderMap = self::$_styleMap['excelBorderMap'];
        return isset($borderMap[$border]) ? $borderMap[$border] : \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE;
    }
    
    /**
     * 儲存格格式
     *
     * @author Mars.Hung <tfaredxj@gmail.com>
     *
     * @param string $format
     *            儲存格格式
     * @return string $excelFormatMap excel儲存格格式
     */
    public static function excelFormatMap($format)
    {
        $formatMap = self::$_styleMap['excelFormatMap'];
        return isset($formatMap[$format]) ? $formatMap[$format] : \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT;
    }
}

<?php
namespace app\libraries\io2\config;

/**
 * NuEIP IO Add Insurance Config
 * 
 * 單一工作表版本
 * 
 * 優化：
 * 1. 動態標題，需
 * 
 * 
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *
 */
class AddInsConfig
{

    /**
     * 標題定義
     *
     * @var array
     */
    private $_title = array();

    /**
     * 內容定義
     *
     * @var array
     */
    private $_content = array();

    /**
     * 結尾定義
     *
     * @var array
     */
    private $_foot = array();
    
    /**
     * 對映表儲存表
     * @var array
     */
    private $_map = array();
    
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
    public function title()
    {
        return $this->_title;
    }
    
    /**
     * 取得標題定義
     *
     * @return array
     */
    public function content()
    {
        return $this->_content;
    }
    
    /**
     * 取得標題定義
     *
     * @return array
     */
    public function foot()
    {
        return $this->_foot;
    }
    
    
    /**
     * **************************************************
     * ************** Map Builder Function **************
     * **************************************************
     */
    
    public function contentKeyMapBuilder()
    {
        
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
    private function titleDefined()
    {
        // 標題1
        $this->_title[] = array(
            'config' => array(
                'type' => 'title',
                'name' => 'title1',
                'style' => 'title1'
            ),
            'defined' => array(
                array(
                    'key' => 't1',
                    'value' => '基本資料',
                    'col' => '12',
                    'row' => '1'
                ),
                array(
                    'key' => 't2',
                    'value' => '勞工保險',
                    'col' => '5',
                    'row' => '1'
                ),
                array(
                    'key' => 't3',
                    'value' => '勞工退休金',
                    'col' => '4',
                    'row' => '1'
                ),
                array(
                    'key' => 't4',
                    'value' => '全民健保',
                    'col' => '3',
                    'row' => '1'
                )
            )
        );
        
        // 標題2
        $this->_title[] = array(
            'config' => array(
                'type' => 'title',
                'name' => 'title2',
                'style' => 'title2'
            ),
            'defined' => array(
                array(
                    'key' => 'u_no',
                    'value' => '員工編號',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'c_name',
                    'value' => '姓名',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'id_no',
                    'value' => '身分證字號',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'birthday',
                    'value' => '出生年月日',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'u_country',
                    'value' => '國別',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'iu_sn',
                    'value' => '投保單位',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'add_date',
                    'value' => '加保日期',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'start_date',
                    'value' => '生效日期',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'ins_status',
                    'value' => '保險身分',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'disability_level',
                    'value' => '身心障礙等級',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'ins_salary',
                    'value' => '月投保薪資',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'remark',
                    'value' => '備註',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'assured_category',
                    'value' => '勞保被保險人類別',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'labor_salary',
                    'value' => '勞保月投保薪資',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'labor_insurance_1',
                    'value' => '參加普通事故保險費',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'labor_insurance_2',
                    'value' => '參加職災保險',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'emp_insurance',
                    'value' => '參加就業保險',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'labor_retir_system',
                    'value' => '勞退制度',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'labor_retir_salary',
                    'value' => '勞退月提繳工資',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'com_withhold_rate',
                    'value' => '公司提繳率',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'emp_withhold_rate',
                    'value' => '個人提繳率',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'ins_category',
                    'value' => '健保投保類別',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'heal_ins_salary',
                    'value' => '健保投保工資',
                    'col' => '1',
                    'row' => '1'
                ),
                array(
                    'key' => 'subsidy_eligibility',
                    'value' => '補助資格',
                    'col' => '1',
                    'row' => '1'
                )
            )
        );
        
        // 範例
        $example = $this->_title[1];
        $example['config']['name'] = 'example';
        $example['config']['style'] = 'example';
        $count = - 1;
        $example['defined'][++ $count]['value'] = '範例列請勿刪除';
        $example['defined'][++ $count]['value'] = '';
        $example['defined'][++ $count]['value'] = '';
        $example['defined'][++ $count]['value'] = '1980/01/01';
        $example['defined'][++ $count]['value'] = '填入國別代碼兩碼如TW';
        $example['defined'][++ $count]['value'] = '';
        $example['defined'][++ $count]['value'] = '1980/01/01';
        $example['defined'][++ $count]['value'] = '1980/01/01';
        $example['defined'][++ $count]['value'] = '員工 / 雇主';
        $example['defined'][++ $count]['value'] = '請選擇';
        $example['defined'][++ $count]['value'] = '請填入金額';
        $example['defined'][++ $count]['value'] = '';
        $example['defined'][++ $count]['value'] = '請選擇';
        $example['defined'][++ $count]['value'] = '沒填系統自動帶合適級距';
        $example['defined'][++ $count]['value'] = '預設是';
        $example['defined'][++ $count]['value'] = '預設是';
        $example['defined'][++ $count]['value'] = '預設是';
        $example['defined'][++ $count]['value'] = '預設新制';
        $example['defined'][++ $count]['value'] = '沒填系統自動帶合適級距';
        $example['defined'][++ $count]['value'] = '%';
        $example['defined'][++ $count]['value'] = '%';
        $example['defined'][++ $count]['value'] = '請選擇';
        $example['defined'][++ $count]['value'] = '沒填系統自動帶合適級距';
        $example['defined'][++ $count]['value'] = '請選擇';
        
        $this->_title[] = $example;
    }

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     */
    private function contentDefined()
    {
        // 內容
        $content = $this->_title[1];
        $content['config']['type'] = 'content';
        $content['config']['name'] = 'content';
        $this->_content = $content;
    }

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    private function footDefined()
    {}
}

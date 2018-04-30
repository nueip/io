<?php
namespace app\libraries\io\config;

/**
 * NuEIP IO Add Insurance Config
 * 
 * 單一工作表版本
 * 
 * 優化：
 * 1. 動態標題，需
 * 
 * 注意：
 * 對建表建構因需要外連SQL，所以為避免第一次使用時還沒建構完成，建議使用建構實例的方式：new AddInsConfig()
 * 
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *
 */
class AddInsConfig
{

    private static $isInited = false;
    
    /**
     * CodeIgniter Instance
     * 
     * @var object
     */
    private static $CI;
    
    /**
     * 標題定義
     *
     * @var array
     */
    public static $_title = array();

    /**
     * 內容定義
     *
     * @var array
     */
    private static $_content = array();

    /**
     * 結尾定義
     *
     * @var array
     */
    private static $_foot = array();
    
    /**
     * 對映表儲存表 - 下拉選單用
     *
     * $_listMap['目標鍵名'] = array(
     * array(
     * 'value' => '數值',
     * 'text' => '數值名稱',
     * ),
     * );
     *
     * @var array
     */
    private static $_listMap = array();
    
    /**
     * 暫存用數
     * @var array
     */
    private static $_cache = array();
    
    /**
     * 資料範本 - 鍵值表及預設值
     * 
     * 如需動態設定預設值時，需取出本表修改後回寫
     * 
     * @var array
     */
    private static $_dataTemplate = array (
        'u_no' => '',
        'c_name' => '',
        'id_no' => '',
        'birthday' => '',
        'u_country' => '',
        'iu_sn' => '',
        'add_date' => '',
        'start_date' => '',
        'ins_status' => '1',
        'disability_level' => '0',
        'ins_salary' => '',
        'remark' => '',
        'assured_category' => '494',
        'labor_salary' => '',
        'labor_insurance_1' => '1',
        'labor_insurance_2' => '1',
        'emp_insurance' => '1',
        'labor_retir_system' => '1',
        'labor_retir_salary' => '',
        'com_withhold_rate' => '0',
        'emp_withhold_rate' => '0',
        'ins_category' => '443',
        'heal_ins_salary' => '',
        'subsidy_eligibility' => '0',
    );
    
    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        // 初始化
        self::initialize();
    }
    
    /**
     * Destruct
     */
    public function __destruct()
    {}
    
    
    /**
     * 重新初始化
     */
    public function reInitialize()
    {
        self::$isInited = false;
        self::initialize();
    }
    
    /**
     * 初始化
     */
    public function initialize()
    {
        // 檢查是否初始化 - 不重複初始化
        if (self::$isInited) {
            return true;
        }
        // 變更檢查旗標
        self::$isInited = true;
        
        // ====== 初始化CI物件 ======
        self::$CI = & get_instance();
        
        // ====== 初始化定義 ======
        self::$_title = array();
        self::$_content = array();
        self::$_foot = array();
        
        self::titleDefined();
        self::contentDefined();
        self::footDefined();
        // ======
        
        // ====== 初始化對映表 ======
        self::$_listMap = array();
        // 對映表建構 - 國別 - u_country
        self::countryMapBuilder();
        // 對映表建構 - 投保單位 - iu_sn
        self::insUnitMapBuilder();
        // 對映表建構 - 保險身份 - ins_status
        self::insStatusMapBuilder();
        // 對映表建構 - 身心障礙等級 - disability_level
        self::disabilityMapBuilder();
        // 對映表建構 - 被保險人類別 - assured_category
        self::assuredMapBuilder();
        // 對映表建構 - 保險是否參加答案 - labor_insurance_1,labor_insurance_2,emp_insurance
        self::insuranceJoinAnserMapBuilder();
        // 對映表建構 - 制度 - labor_retir_system
        self::retirSystemMapBuilder();
        // 對映表建構 - 投保類別 - ins_category
        self::insCategoryMapBuilder();
        // 對映表建構 - 本人符合補助資格 - subsidy_eligibility
        self::subsidyMapBuilder();
        // 對映表建構 - 級距 - 勞保、勞退、健保
        self::levelMapBuilder();
        // ======
        
        return true;
    }
    
    /**
     * **********************************************
     * ************** Setting Function **************
     * **********************************************
     */
    
    /**
     * 標題定義 - 取得/設定
     * 
     * 取得：無參數時
     * 設定：有參數時
     * 
     * @return array
     */
    public static function title($data = null)
    {
        if (!is_null($data)) {
            self::$_title = $data;
        } elseif (empty(self::$_title)) {
            self::titleDefined();
        }
        
        return self::$_title;
    }
    
    /**
     * 內容定義 - 取得/設定
     * 
     * 取得：無參數時
     * 設定：有參數時
     * 
     * @return array
     */
    public static function content($data = null)
    {
        if (!is_null($data)) {
            self::$_content = $data;
        } elseif (empty(self::$_content)) {
            self::contentDefined();
        }
        
        return self::$_content;
    }
    
    /**
     * 結尾定義 - 取得/設定
     * 
     * 取得：無參數時
     * 設定：有參數時
     * 
     * @return array
     */
    public static function foot($data = null)
    {
        if (!is_null($data)) {
            self::$_foot = $data;
        } elseif (empty(self::$_foot)) {
            self::footDefined();
        }
        
        return self::$_foot;
    }
    
    /**
     * 資料範本 - 鍵值表及預設值 - 取得/設定
     *
     * 如需動態設定預設值時，需取出本表修改後回寫
     *
     * 取得：無參數時
     * 設定：有參數時
     *
     * @return array
     */
    public static function dataTemplate($data = null)
    {
        if (!is_null($data)) {
            self::$_dataTemplate = $data;
        }
        
        return self::$_dataTemplate;
    }
    
    /**
     * 資料範本 - 鍵值表及預設值 - 取得/設定
     *
     * 如需動態設定預設值時，需取出本表修改後回寫
     *
     * 取得：無參數時
     * 設定：有參數時
     *
     * @return array
     */
    public static function getList()
    {
        return self::$_listMap;
    }
    
    /**
     * ******************************************************
     * ************** Content Process Function **************
     * ******************************************************
     */
    
    /**
     * 內容整併 - 以資料內容範本為模版合併資料
     * 
     * 整併原始資料後，如需要多餘資料執行額外處理，可在處理完後再執行內容過濾 self::contentFilter($data);
     * 
     * @param array $data 原始資料內容
     * @return \app\libraries\io\config\AddInsConfig
     */
    public static function contentRefactor(Array & $data)
    {
        // 將現有對映表轉成value=>text格式存入暫存
        self::value2TextMapBuilder();
        
        foreach ($data as $key => &$row) {
            $row = (array)$row;
            
            // 以資料內容範本為模版合併資料
            $row = array_merge(self::$_dataTemplate, $row);
            
            // 內容整併處理時執行 - 迴圈內自定步驟
            self::onRefactor($key, $row);
            
            // 執行資料轉換 value => text
            self::value2Text($key, $row);
        }
        
        return new static();
    }
    
    /**
     * 內容過濾 - 以資料內容範本為模版過濾多餘資料
     * 
     * 將不需要的多餘資料濾除，通常處理self::contentRefactor($data)整併完的內容
     * 
     * @param array $data 原始資料內容
     * @return \app\libraries\io\config\AddInsConfig
     */
    public static function contentFilter(Array & $data)
    {
        foreach ($data as $key => &$row) {
            $row = (array)$row;
            
            // 以資料內容範本為模版過濾多餘資料
            $row = array_intersect_key($row, self::$_dataTemplate);
        }
        
        return new static();
    }
    
    /**
     * 執行資料轉換 value => text
     *
     * @param string $key 當次迴圈的Key值
     * @param array $row 當次迴圈的內容
     */
    public static function value2Text($key, &$row)
    {
        // 遍歷資料，並轉換內容
        foreach ($row as $k => &$v) {
            // 檢查是否需要內容轉換
            if (!isset(self::$_cache['value2Text'][$k])) {
                continue;
            }
            
            // 處理資料轉換
            $v = isset(self::$_cache['value2Text'][$k][$v]) ? self::$_cache['value2Text'][$k][$v] : '';
        }
    }
    
    /**
     * 內容整併處理時執行 - 迴圈內自定步驟
     *
     * @param string $key 當次迴圈的Key值
     * @param array $row 當次迴圈的內容
     */
    public static function onRefactor($key, &$row)
    {
        // 設定加保日期
        if (isset($row['arrive_date'])) {
            $row['add_date'] = $row['arrive_date'];
        }
        
        // 設定投保單位 - 預設第一個投保單位
        $row['iu_sn'] = key(self::$_listMap['rate_company']);
        // 設定公司提繳率 - 預設第一個投保單位 - 因為無法聯動公司選項來變更提繳率
        $row['com_withhold_rate'] = current(self::$_listMap['rate_company']);
    }
    
    /**
     * ******************************************
     * ************** Map Function **************
     * ******************************************
     */
    
    
    
    /**
     * **************************************************
     * ************** Map Builder Function **************
     * **************************************************
     */
    
    /**
     * 將現有對映表轉成value=>text格式存入暫存
     */
    public static function value2TextMapBuilder()
    {
        // 初始化暫存
        self::$_cache['value2Text'] = array();
        
        foreach (self::$_listMap as $key => $map) {
            self::$_cache['value2Text'][$key] = array_column($map, 'text', 'value');
        }
    }
    
    /**
     * 將現有對映表轉成text=>value格式存入暫存
     */
    public static function text2ValueMapBuilder()
    {
        // 初始化暫存
        self::$_cache['text2Value'] = array();
        
        foreach (self::$_listMap as $key => $map) {
            self::$_cache['text2Value'][$key] = array_column($map, 'value', 'text');
        }
    }
    
    /**
     * 對映表建構 - 國別 - u_country
     */
    public static function countryMapBuilder()
    {
        // 取得國家代碼表
        self::$CI->load->library('Ins_data_component');
        $cData = self::$CI->ins_data_component->countryCodeData();
        
        // 資料整理
        $data = array();
        foreach ($cData as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_1'],
            );
        }
        
        // 寫入對映表
        self::$_listMap['u_country'] = $data;
    }
    
    /**
     * 對映表建構 - 投保單位 & 公司扣繳率 - iu_sn
     */
    public static function insUnitMapBuilder()
    {
        // 取得投保單位
        self::$CI->load->model('Ins_setting_tw_model');
        $insUnit = self::$CI->Ins_setting_tw_model->find()
        ->select('s_sn,c_name,rate_company')
        ->get()
        ->result_array();
        
        // 資料整理
        $data = array();
        $rData = array();
        foreach ($insUnit as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['c_name'],
            );
            $rData[$v['s_sn']] = $v['rate_company'] - 0;
        }
        
        // 寫入對映表
        self::$_listMap['iu_sn'] = $data;
        self::$_listMap['rate_company'] = $rData;
    }
    
    /**
     * 對映表建構 - 保險身份 - ins_status
     */
    public static function insStatusMapBuilder()
    {
        // 寫入對映表
        self::$_listMap['ins_status'] = array(
            array(
                'value' => '1',
                'text' => '員工',
            ),
            array(
                'value' => '2',
                'text' => '雇主',
            ),
        );
    }
    
    /**
     * 對映表建構 - 身心障礙等級 - disability_level
     */
    public static function disabilityMapBuilder()
    {
        // 取得規則資料
        self::$CI->load->library('Ins_data_component');
        $dData = self::$CI->ins_data_component->queryDetailByCode(4, 3, false, [
            'patch' => 1
        ])
        ->select('s_sn,value_4')
        ->order_by('s_sn', 'ASC')
        ->get()
        ->result_array();
        
        // 資料整理
        $data = array(
            array(
                'value' => '0',
                'text' => '無',
            )
        );
        // 增加選項 - 無
        foreach ($dData as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_4'],
            );
        }
        
        // 寫入對映表
        self::$_listMap['disability_level'] = $data;
    }
    
    /**
     * 對映表建構 - 被保險人類別 - assured_category
     *
     * 批次加保為辨別哪些保險別不需加保，在 勞保被保險人類別、勞退制度、健保投保類別 三個選單的最後加上「不投保」選項
     */
    public static function assuredMapBuilder()
    {
        // 取得規則資料
        self::$CI->load->library('Ins_data_component');
        $aData = $query = self::$CI->ins_data_component->queryDetailByCode(2, 1)
        ->select('s_sn,value_4')
        ->order_by('s_sn', 'ASC')
        ->get()
        ->result_array();
        
        // 資料整理
        $data = array();
        foreach ($aData as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_4'],
            );
        }
        
        // 增加不投保選項
        $data[] = array(
            'value' => '',
            'text' => '不投保',
        );
        
        // 寫入對映表
        self::$_listMap['assured_category'] = $data;
    }
    
    /**
     * 對映表建構 - 保險是否參加答案 - labor_insurance_1,labor_insurance_2,emp_insurance
     */
    public static function insuranceJoinAnserMapBuilder()
    {
        self::$_listMap['labor_insurance_1'] = array(
            array(
                'value' => '1',
                'text' => '是',
            ),
            array(
                'value' => '0',
                'text' => '否',
            ),
        );
        
        self::$_listMap['labor_insurance_2'] = self::$_listMap['labor_insurance_1'];
        
        self::$_listMap['emp_insurance'] = self::$_listMap['labor_insurance_1'];
    }
    
    /**
     * 對映表建構 - 制度 - labor_retir_system
     *
     * 批次加保為辨別哪些保險別不需加保，在 勞保被保險人類別、勞退制度、健保投保類別 三個選單的最後加上「不投保」選項
     */
    public static function retirSystemMapBuilder()
    {
        // 寫入對映表
        self::$_listMap['labor_retir_system'] = array(
            array(
                'value' => '1',
                'text' => '新制',
            ),
            array(
                'value' => '2',
                'text' => '舊制',
            ),
            array(
                'value' => '0',
                'text' => '無須提繳',
            ),
            array(
                'value' => '',
                'text' => '不投保',
            ),
        );
    }
    
    /**
     * 對映表建構 - 投保類別 - ins_category
     *
     * 批次加保為辨別哪些保險別不需加保，在 勞保被保險人類別、勞退制度、健保投保類別 三個選單的最後加上「不投保」選項
     */
    public static function insCategoryMapBuilder()
    {
        // 取得規則資料
        self::$CI->load->library('Ins_data_component');
        $nhiCategory = self::$CI->ins_data_component->getNhiCategoryByDateRange('0000-00-00', '9999-12-31', array(
            's_sn',
            'start_date',
            'end_date',
            'value_4'
        ));
        
        // 資料整理
        $data = array();
        foreach ($nhiCategory as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_4'],
            );
        }
        
        // 增加選項-不投保
        $data[] = array(
            'value' => '',
            'text' => '不投保',
        );
        
        // 寫入對映表
        self::$_listMap['ins_category'] = $data;
    }
    
    /**
     * 對映表建構 - 本人符合補助資格 - subsidy_eligibility
     */
    public static function subsidyMapBuilder()
    {
        // 取得規則資料
        self::$CI->load->library('Ins_data_component');
        $nhiSubsidy = self::$CI->ins_data_component->queryDetailByCode(4, 13, false, [
            'patch' => 1
        ])
        ->select('s_sn,value_4')
        ->order_by('data_sort', 'ASC')
        ->order_by('value_1', 'ASC')
        ->get()
        ->result_array();
        
        // 資料整理
        $data = array(
            array(
                'value' => '0',
                'text' => '無',
            )
        );
        foreach ($nhiSubsidy as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_4'],
            );
        }
        
        // 寫入對映表
        self::$_listMap['subsidy_eligibility'] = $data;
    }
    
    /**
     * 對映表建構 - 級距 - 勞保、勞退、健保
     * 
     * 需要有歷史表及目前生效表
     * 匯出時，使用目前生效表
     * 匯入時，需使用歷史表檢查
     * 
     * @param date $dateRangeStart
     *            參考時間-起
     * @param date $dateRangeEnd
     *            參考時間-訖
     */
    public static function levelMapBuilder($dateRangeStart = null, $dateRangeEnd = null)
    {
        self::$CI->load->library('tw_ins_management/Tw_ins_management_component');
        self::$CI->load->library('Dbfunctions');
        
        // 時間處理
        $today = self::$CI->dbfunctions->getTime_zone('', 'Y-m-d');
        $dateRangeStart = is_null($dateRangeStart) ? $today : $dateRangeStart;
        $dateRangeEnd = is_null($dateRangeEnd) ? $today : $dateRangeEnd;
        
        // 對映表建構 - 勞保級距 - 使用參考日期區間
        $data = self::$CI->tw_ins_level_adjust_component->laborLevelMapBuilder($dateRangeStart, $dateRangeEnd);
        // 資料整理
        $tData = array();
        $lastEffectDate = '0000-00-00';
        foreach ($data as $date => $v) {
            foreach ($v as $lv => $vv) {
                $tData[$date][] = array(
                    'value' => $lv,
                    'text' => $lv,
                );
            }
            // 取得今日有效的規則日期
            $lastEffectDate = $date <= $today ? $date : $lastEffectDate;
        }
        // 總表
        self::$_listMap['laborLevel'] = $tData;
        // 目前生效的表
        self::$_listMap['labor_salary'] = $tData[$lastEffectDate];
        
        // 對映表建構 - 勞退級距 - 使用參考日期區間
        $data = self::$CI->tw_ins_level_adjust_component->pensionLevelMapBuilder($dateRangeStart, $dateRangeEnd);
        // 資料整理
        $tData = array();
        $lastEffectDate = '0000-00-00';
        foreach ($data as $date => $v) {
            foreach ($v as $lv => $vv) {
                $tData[$date][] = array(
                    'value' => $lv,
                    'text' => $lv,
                );
            }
            // 取得今日有效的規則日期
            $lastEffectDate = $date <= $today ? $date : $lastEffectDate;
        }
        // 總表
        self::$_listMap['pensionLevel'] = $tData;
        // 目前生效的表
        self::$_listMap['labor_retir_salary'] = $tData[$lastEffectDate];
        
        // 對映表建構 - 健保級距 - 使用參考日期區間
        $data = self::$CI->tw_ins_level_adjust_component->nhiLevelMapBuilder($dateRangeStart, $dateRangeEnd);
        // 資料整理
        $tData = array();
        $lastEffectDate = '0000-00-00';
        foreach ($data as $date => $v) {
            foreach ($v as $lv => $vv) {
                $tData[$date][] = array(
                    'value' => $lv,
                    'text' => $lv,
                );
            }
            // 取得今日有效的規則日期
            $lastEffectDate = $date <= $today ? $date : $lastEffectDate;
        }
        // 總表
        self::$_listMap['nhiLevel'] = $tData;
        // 目前生效的表
        self::$_listMap['heal_ins_salary'] = $tData[$lastEffectDate];
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
    private static function titleDefined()
    {
        // 標題1
        self::$_title[] = array(
            'config' => array(
                'type' => 'title',
                'name' => 'title1',
                'style' => array(
                    'font-size' => '16',
                ),
                'class' => 'title1'
            ),
            'defined' => array(
                't1' => array(
                    'key' => 't1',
                    'value' => '基本資料',
                    'col' => '12',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => '',
                ),
                't2' => array(
                    'key' => 't2',
                    'value' => '勞工保險',
                    'col' => '5',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => '',
                ),
                't3' => array(
                    'key' => 't3',
                    'value' => '勞工退休金',
                    'col' => '4',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => '',
                ),
                't4' => array(
                    'key' => 't4',
                    'value' => '全民健保',
                    'col' => '3',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => '',
                )
            )
        );
        
        // 標題2
        self::$_title[] = array(
            'config' => array(
                'type' => 'title',
                'name' => 'title2',
                'style' => array(),
                'class' => 'title2'
            ),
            'defined' => array(
                'u_no' => array(
                    'key' => 'u_no',
                    'value' => '員工編號',
                    'col' => '1',
                    'row' => '1'
                ),
                'c_name' => array(
                    'key' => 'c_name',
                    'value' => '姓名',
                    'col' => '1',
                    'row' => '1'
                ),
                'id_no' => array(
                    'key' => 'id_no',
                    'value' => '身分證字號',
                    'col' => '1',
                    'row' => '1'
                ),
                'birthday' => array(
                    'key' => 'birthday',
                    'value' => '出生年月日',
                    'col' => '1',
                    'row' => '1'
                ),
                'u_country' => array(
                    'key' => 'u_country',
                    'value' => '國別',
                    'col' => '1',
                    'row' => '1'
                ),
                'iu_sn' => array(
                    'key' => 'iu_sn',
                    'value' => '投保單位',
                    'col' => '1',
                    'row' => '1'
                ),
                'add_date' => array(
                    'key' => 'add_date',
                    'value' => '加保日期',
                    'col' => '1',
                    'row' => '1'
                ),
                'start_date' => array(
                    'key' => 'start_date',
                    'value' => '生效日期',
                    'col' => '1',
                    'row' => '1'
                ),
                'ins_status' => array(
                    'key' => 'ins_status',
                    'value' => '保險身分',
                    'col' => '1',
                    'row' => '1'
                ),
                'disability_level' => array(
                    'key' => 'disability_level',
                    'value' => '身心障礙等級',
                    'col' => '1',
                    'row' => '1'
                ),
                'ins_salary' => array(
                    'key' => 'ins_salary',
                    'value' => '月投保薪資',
                    'col' => '1',
                    'row' => '1'
                ),
                'remark' => array(
                    'key' => 'remark',
                    'value' => '備註',
                    'col' => '1',
                    'row' => '1'
                ),
                'assured_category' => array(
                    'key' => 'assured_category',
                    'value' => '勞保被保險人類別',
                    'col' => '1',
                    'row' => '1'
                ),
                'labor_salary' => array(
                    'key' => 'labor_salary',
                    'value' => '勞保月投保薪資',
                    'col' => '1',
                    'row' => '1'
                ),
                'labor_insurance_1' => array(
                    'key' => 'labor_insurance_1',
                    'value' => '參加普通事故保險費',
                    'col' => '1',
                    'row' => '1'
                ),
                'labor_insurance_2' => array(
                    'key' => 'labor_insurance_2',
                    'value' => '參加職災保險',
                    'col' => '1',
                    'row' => '1'
                ),
                'emp_insurance' => array(
                    'key' => 'emp_insurance',
                    'value' => '參加就業保險',
                    'col' => '1',
                    'row' => '1'
                ),
                'labor_retir_system' => array(
                    'key' => 'labor_retir_system',
                    'value' => '勞退制度',
                    'col' => '1',
                    'row' => '1'
                ),
                'labor_retir_salary' => array(
                    'key' => 'labor_retir_salary',
                    'value' => '勞退月提繳工資',
                    'col' => '1',
                    'row' => '1'
                ),
                'com_withhold_rate' => array(
                    'key' => 'com_withhold_rate',
                    'value' => '公司提繳率',
                    'col' => '1',
                    'row' => '1'
                ),
                'emp_withhold_rate' => array(
                    'key' => 'emp_withhold_rate',
                    'value' => '個人提繳率',
                    'col' => '1',
                    'row' => '1'
                ),
                'ins_category' => array(
                    'key' => 'ins_category',
                    'value' => '健保投保類別',
                    'col' => '1',
                    'row' => '1'
                ),
                'heal_ins_salary' => array(
                    'key' => 'heal_ins_salary',
                    'value' => '健保投保工資',
                    'col' => '1',
                    'row' => '1'
                ),
                'subsidy_eligibility' => array(
                    'key' => 'subsidy_eligibility',
                    'value' => '補助資格',
                    'col' => '1',
                    'row' => '1'
                )
            )
        );
        
        // 範例
        $example = self::$_title[1];
        $example['config']['name'] = 'example';
        $example['config']['style'] = array();
        $example['config']['class'] = 'example';
        $example['defined']['u_no']['value'] = '範例列請勿刪除';
        $example['defined']['c_name']['value'] = '';
        $example['defined']['id_no']['value'] = '';
        $example['defined']['birthday']['value'] = '1980/01/01';
        $example['defined']['u_country']['value'] = '填入國別代碼兩碼如TW';
        $example['defined']['iu_sn']['value'] = '';
        $example['defined']['add_date']['value'] = '1980/01/01';
        $example['defined']['start_date']['value'] = '1980/01/01';
        $example['defined']['ins_status']['value'] = '員工 / 雇主';
        $example['defined']['disability_level']['value'] = '請選擇';
        $example['defined']['ins_salary']['value'] = '請填入金額';
        $example['defined']['remark']['value'] = '';
        $example['defined']['assured_category']['value'] = '請選擇';
        $example['defined']['labor_salary']['value'] = '沒填系統自動帶合適級距';
        $example['defined']['labor_insurance_1']['value'] = '預設是';
        $example['defined']['labor_insurance_2']['value'] = '預設是';
        $example['defined']['emp_insurance']['value'] = '預設是';
        $example['defined']['labor_retir_system']['value'] = '預設新制';
        $example['defined']['labor_retir_salary']['value'] = '沒填系統自動帶合適級距';
        $example['defined']['com_withhold_rate']['value'] = '%';
        $example['defined']['emp_withhold_rate']['value'] = '%';
        $example['defined']['ins_category']['value'] = '請選擇';
        $example['defined']['heal_ins_salary']['value'] = '沒填系統自動帶合適級距';
        $example['defined']['subsidy_eligibility']['value'] = '請選擇';
        
        self::$_title[] = $example;
    }

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     */
    private static function contentDefined()
    {
        // 內容
        $content = self::$_title[1];
        $content['config']['type'] = 'content';
        $content['config']['name'] = 'content';
        $content['config']['style'] = array();
        $content['config']['class'] = 'content';
        $content['defined']['u_no']['style'] = array('background-color' => 'FFDBDCDC');
        $content['defined']['c_name']['style'] = array('background-color' => 'FFDBDCDC');
        $content['defined']['ins_salary']['style'] = array('format' => 'number');
        $content['defined']['labor_salary']['style'] = array('format' => 'number');
        $content['defined']['labor_retir_salary']['style'] = array('format' => 'number');
        $content['defined']['heal_ins_salary']['style'] = array('format' => 'number');
        self::$_content = $content;
    }

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    private static function footDefined()
    {}
}

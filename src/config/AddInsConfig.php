<?php
namespace marshung\io\config;

/**
 * 勞健保-批次加保範本
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
class AddInsConfig extends \marshung\io\config\abstracts\Config
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
        $this->_options['sheetName'] = '批次加保範本';
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
    {
        // 設定加保日期
        if (isset($row['arrive_date'])) {
            $row['add_date'] = $row['arrive_date'];
        }
        
        // 設定投保單位 - 預設第一個投保單位
        $row['iu_sn'] = key($this->_listMap['rate_company']);
        // 設定公司提繳率 - 預設第一個投保單位 - 因為無法聯動公司選項來變更提繳率
        $row['com_withhold_rate'] = current($this->_listMap['rate_company']);
    }

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
        // 對映表建構 - 國別 - u_country
        $this->countryMapBuilder();
        // 對映表建構 - 投保單位 - iu_sn
        $this->insUnitMapBuilder();
        // 對映表建構 - 保險身份 - ins_status
        $this->insStatusMapBuilder();
        // 對映表建構 - 身心障礙等級 - disability_level
        $this->disabilityMapBuilder();
        // 對映表建構 - 被保險人類別 - assured_category
        $this->assuredMapBuilder();
        // 對映表建構 - 保險是否參加答案 - labor_insurance_1,labor_insurance_2,emp_insurance
        $this->insuranceJoinAnserMapBuilder();
        // 對映表建構 - 制度 - labor_retir_system
        $this->retirSystemMapBuilder();
        // 對映表建構 - 投保類別 - ins_category
        $this->insCategoryMapBuilder();
        // 對映表建構 - 本人符合補助資格 - subsidy_eligibility
        $this->subsidyMapBuilder();
        // 對映表建構 - 級距 - 勞保、勞退、健保
        $this->levelMapBuilder();
    }

    /**
     * 對映表建構 - 國別 - u_country
     */
    protected function countryMapBuilder()
    {
        // 取得國家代碼表
        $this->load->library('Ins_data_component');
        $cData = $this->ins_data_component->countryCodeData();
        
        // 資料整理
        $data = array();
        foreach ($cData as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_1']
            );
        }
        
        // 寫入對映表
        $this->_listMap['u_country'] = $data;
    }

    /**
     * 對映表建構 - 投保單位 & 公司扣繳率 - iu_sn
     */
    protected function insUnitMapBuilder()
    {
        // 取得投保單位
        $this->load->model('Ins_setting_tw_model');
        $insUnit = $this->Ins_setting_tw_model->find()
            ->select('s_sn,c_name,rate_company')
            ->get()
            ->result_array();
        
        // 資料整理
        $data = array();
        $rData = array();
        foreach ($insUnit as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['c_name']
            );
            $rData[$v['s_sn']] = $v['rate_company'] - 0;
        }
        
        // 寫入對映表
        $this->_listMap['iu_sn'] = $data;
        $this->_listMap['rate_company'] = $rData;
    }

    /**
     * 對映表建構 - 保險身份 - ins_status
     */
    protected function insStatusMapBuilder()
    {
        // 寫入對映表
        $this->_listMap['ins_status'] = array(
            array(
                'value' => '1',
                'text' => '員工'
            ),
            array(
                'value' => '2',
                'text' => '雇主'
            )
        );
    }

    /**
     * 對映表建構 - 身心障礙等級 - disability_level
     */
    protected function disabilityMapBuilder()
    {
        // 取得規則資料
        $this->load->library('Ins_data_component');
        $dData = $this->ins_data_component->queryDetailByCode(4, 3, false, [
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
                'text' => '無'
            )
        );
        // 增加選項 - 無
        foreach ($dData as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_4']
            );
        }
        
        // 寫入對映表
        $this->_listMap['disability_level'] = $data;
    }

    /**
     * 對映表建構 - 被保險人類別 - assured_category
     *
     * 批次加保為辨別哪些保險別不需加保，在 勞保被保險人類別、勞退制度、健保投保類別 三個選單的最後加上「不投保」選項
     */
    protected function assuredMapBuilder()
    {
        // 取得規則資料
        $this->load->library('Ins_data_component');
        $aData = $query = $this->ins_data_component->queryDetailByCode(2, 1)
            ->select('s_sn,value_4')
            ->order_by('s_sn', 'ASC')
            ->get()
            ->result_array();
        
        // 資料整理
        $data = array();
        foreach ($aData as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_4']
            );
        }
        
        // 增加不投保選項
        $data[] = array(
            'value' => '',
            'text' => '不投保'
        );
        
        // 寫入對映表
        $this->_listMap['assured_category'] = $data;
    }

    /**
     * 對映表建構 - 保險是否參加答案 - labor_insurance_1,labor_insurance_2,emp_insurance
     */
    protected function insuranceJoinAnserMapBuilder()
    {
        $this->_listMap['labor_insurance_1'] = array(
            array(
                'value' => '1',
                'text' => '是'
            ),
            array(
                'value' => '0',
                'text' => '否'
            )
        );
        
        $this->_listMap['labor_insurance_2'] = $this->_listMap['labor_insurance_1'];
        
        $this->_listMap['emp_insurance'] = $this->_listMap['labor_insurance_1'];
    }

    /**
     * 對映表建構 - 制度 - labor_retir_system
     *
     * 批次加保為辨別哪些保險別不需加保，在 勞保被保險人類別、勞退制度、健保投保類別 三個選單的最後加上「不投保」選項
     */
    protected function retirSystemMapBuilder()
    {
        // 寫入對映表
        $this->_listMap['labor_retir_system'] = array(
            array(
                'value' => '1',
                'text' => '新制'
            ),
            array(
                'value' => '2',
                'text' => '舊制'
            ),
            array(
                'value' => '0',
                'text' => '無須提繳'
            ),
            array(
                'value' => '',
                'text' => '不投保'
            )
        );
    }

    /**
     * 對映表建構 - 投保類別 - ins_category
     *
     * 批次加保為辨別哪些保險別不需加保，在 勞保被保險人類別、勞退制度、健保投保類別 三個選單的最後加上「不投保」選項
     */
    protected function insCategoryMapBuilder()
    {
        // 取得規則資料
        $this->load->library('Ins_data_component');
        $nhiCategory = $this->ins_data_component->getNhiCategoryByDateRange('0000-00-00', '9999-12-31', array(
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
                'text' => $v['value_4']
            );
        }
        
        // 增加選項-不投保
        $data[] = array(
            'value' => '',
            'text' => '不投保'
        );
        
        // 寫入對映表
        $this->_listMap['ins_category'] = $data;
    }

    /**
     * 對映表建構 - 本人符合補助資格 - subsidy_eligibility
     */
    protected function subsidyMapBuilder()
    {
        // 取得規則資料
        $this->load->library('Ins_data_component');
        $nhiSubsidy = $this->ins_data_component->queryDetailByCode(4, 13, false, [
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
                'text' => '無'
            )
        );
        foreach ($nhiSubsidy as $k => $v) {
            $data[] = array(
                'value' => $v['s_sn'],
                'text' => $v['value_4']
            );
        }
        
        // 寫入對映表
        $this->_listMap['subsidy_eligibility'] = $data;
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
    protected function levelMapBuilder($dateRangeStart = null, $dateRangeEnd = null)
    {
        $this->load->library('tw_ins_management/Tw_ins_management_component');
        $this->load->library('Dbfunctions');
        
        // 時間處理
        $today = $this->dbfunctions->getTime_zone('', 'Y-m-d');
        $dateRangeStart = is_null($dateRangeStart) ? $today : $dateRangeStart;
        $dateRangeEnd = is_null($dateRangeEnd) ? $today : $dateRangeEnd;
        
        // 對映表建構 - 勞保級距 - 使用參考日期區間
        $data = $this->tw_ins_level_adjust_component->laborLevelMapBuilder($dateRangeStart, $dateRangeEnd);
        // 資料整理
        $tData = array();
        $lastEffectDate = '0000-00-00';
        foreach ($data as $date => $v) {
            foreach ($v as $lv => $vv) {
                $tData[$date][] = array(
                    'value' => $lv,
                    'text' => $lv
                );
            }
            // 取得今日有效的規則日期
            $lastEffectDate = $date <= $today ? $date : $lastEffectDate;
        }
        // 總表
        $this->_listMap['laborLevel'] = $tData;
        // 目前生效的表
        $this->_listMap['labor_salary'] = $tData[$lastEffectDate];
        
        // 對映表建構 - 勞退級距 - 使用參考日期區間
        $data = $this->tw_ins_level_adjust_component->pensionLevelMapBuilder($dateRangeStart, $dateRangeEnd);
        // 資料整理
        $tData = array();
        $lastEffectDate = '0000-00-00';
        foreach ($data as $date => $v) {
            foreach ($v as $lv => $vv) {
                $tData[$date][] = array(
                    'value' => $lv,
                    'text' => $lv
                );
            }
            // 取得今日有效的規則日期
            $lastEffectDate = $date <= $today ? $date : $lastEffectDate;
        }
        // 總表
        $this->_listMap['pensionLevel'] = $tData;
        // 目前生效的表
        $this->_listMap['labor_retir_salary'] = $tData[$lastEffectDate];
        
        // 對映表建構 - 健保級距 - 使用參考日期區間
        $data = $this->tw_ins_level_adjust_component->nhiLevelMapBuilder($dateRangeStart, $dateRangeEnd);
        // 資料整理
        $tData = array();
        $lastEffectDate = '0000-00-00';
        foreach ($data as $date => $v) {
            foreach ($v as $lv => $vv) {
                $tData[$date][] = array(
                    'value' => $lv,
                    'text' => $lv
                );
            }
            // 取得今日有效的規則日期
            $lastEffectDate = $date <= $today ? $date : $lastEffectDate;
        }
        // 總表
        $this->_listMap['nhiLevel'] = $tData;
        // 目前生效的表
        $this->_listMap['heal_ins_salary'] = $tData[$lastEffectDate];
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
        $title = array();
        
        // 標題1
        $title[] = array(
            'config' => array(
                'type' => 'title',
                'name' => 'title1',
                'style' => array(
                    'font-size' => '16'
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
                    'list' => ''
                ),
                't2' => array(
                    'key' => 't2',
                    'value' => '勞工保險',
                    'col' => '5',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't3' => array(
                    'key' => 't3',
                    'value' => '勞工退休金',
                    'col' => '4',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't4' => array(
                    'key' => 't4',
                    'value' => '全民健保',
                    'col' => '3',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                )
            )
        );
        
        // 標題2
        $title[] = array(
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
        $example = $title[1];
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
        
        $title[] = $example;
        
        // 設定標題定義
        $this->setTitle($title);
    }

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     */
    protected function contentDefined()
    {
        $title = $this->getTitle();
        
        // 內容
        $content = $title[1];
        $content['config']['type'] = 'content';
        $content['config']['name'] = 'content';
        $content['config']['style'] = array();
        $content['config']['class'] = 'content';
        // Style
        $content['defined']['u_no']['style'] = array(
            'background-color' => 'FFDBDCDC'
        );
        $content['defined']['c_name']['style'] = array(
            'background-color' => 'FFDBDCDC'
        );
        $content['defined']['ins_salary']['style'] = array(
            'format' => 'number'
        );
        $content['defined']['labor_salary']['style'] = array(
            'format' => 'number'
        );
        $content['defined']['labor_retir_salary']['style'] = array(
            'format' => 'number'
        );
        $content['defined']['heal_ins_salary']['style'] = array(
            'format' => 'number'
        );
        
        // Default
        $content['defined']['ins_status']['default'] = '1';
        $content['defined']['disability_level']['default'] = '0';
        $content['defined']['assured_category']['default'] = '494';
        $content['defined']['labor_insurance_1']['default'] = '1';
        $content['defined']['labor_insurance_2']['default'] = '1';
        $content['defined']['emp_insurance']['default'] = '1';
        $content['defined']['labor_retir_system']['default'] = '1';
        $content['defined']['labor_retir_salary']['default'] = '';
        $content['defined']['com_withhold_rate']['default'] = '0';
        $content['defined']['emp_withhold_rate']['default'] = '0';
        $content['defined']['ins_category']['default'] = '443';
        $content['defined']['subsidy_eligibility']['default'] = '0';
        
        // 設定內容定義
        $this->setContent($content);
    }

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    protected function footDefined()
    {
        $foot = array();
        
        // 設定結尾定義
        $this->setContent($foot);
    }
}

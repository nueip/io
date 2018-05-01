<?php
namespace app\libraries\io\config;

/**
 * NuEIP IO Config - 薪資結算-步驟8非固定薪資匯入
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
class SalaryViiiConfig extends \app\libraries\io\config\abstracts\Config
{

    /**
     * 設定檔版號
     *
     * @var number
     */
    protected static $_version = 0.1;

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
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
    {}

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
        // 對映表建構 - 科目 - payroll_item
        $this->payrollMapBuilder();
    }

    /**
     * 對映表建構 - 科目 - payroll_item
     */
    protected function payrollMapBuilder()
    {
        $this->CI->load->model('Payroll_item_model');
        $conpanyID = $this->CI->config->item('Company');
        
        // 取得一般科目資料
        $this->CI->db->order_by('s_sort');
        $pData = $this->CI->Payroll_item_model->getPayrollItem(null, $conpanyID, $col = 's_sn,item_name');
        
        // 資料整理
        $data = array();
        foreach ($pData as $k => $v) {
            $data[] = array(
                'value' => $v->s_sn,
                'text' => $v->item_name
            );
        }
        
        // 寫入對映表
        $this->_listMap['payroll_item'] = $data;
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
        // 標題1
        $this->_title[] = array(
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
                    'value' => '員工編號',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't2' => array(
                    'key' => 't2',
                    'value' => '姓名',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't3' => array(
                    'key' => 't3',
                    'value' => '科目名稱',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                't4' => array(
                    'key' => 't4',
                    'value' => '金額',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                )
            )
        );
    }

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     */
    protected function contentDefined()
    {
        // 內容
        $this->_content = array(
            'config' => array(
                'type' => 'content',
                'name' => 'content',
                'style' => array(),
                'class' => ''
            ),
            'defined' => array(
                'u_no' => array(
                    'key' => 'u_no',
                    'value' => '員工編號',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(
                        'background-color' => 'FFDBDCDC'
                    ),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                'c_name' => array(
                    'key' => 'c_name',
                    'value' => '姓名',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(
                        'background-color' => 'FFDBDCDC'
                    ),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                'payroll_item' => array(
                    'key' => 'payroll_item',
                    'value' => '科目名稱',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                ),
                'amount' => array(
                    'key' => 'amount',
                    'value' => '金額',
                    'col' => '1',
                    'row' => '1',
                    'style' => array(
                        'format' => 'number'
                    ),
                    'class' => '',
                    'default' => '',
                    'list' => ''
                )
            )
        );
    }

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    protected function footDefined()
    {}
}

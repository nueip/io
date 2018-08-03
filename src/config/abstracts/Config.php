<?php
namespace nueip\io\config\abstracts;

/**
 * IO Config abstract
 *
 * 規則：
 * 1. title,foot二種資料，一列一筆定義
 * 2. content種類資料，多列一筆定義
 * 3. 如果沒有設定title,foot定義，則不處理該種類資料
 * 4. 如果沒有設定content定義，則傳入資料不做過濾
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-18
 *        
 */
abstract class Config
{

    /**
     * 設定檔參數
     *
     * @var array
     */
    protected $_options = array(
        'abstractVersion' => '0.1',
        'version' => '0.1',
        'minVersion' => '0.1',
        'configName' => __CLASS__,
        'sheetName' => 'Worksheet',
        // 模式：簡易(simple)、複雜(complex)
        'type' => 'simple',
        // 必要欄位，必需有值，提供讀取資料時驗証用 - 有設定，且必要欄位有無資料者，跳出 - 因各版本excel對空列定義不同，可能編輯過列，就會產生沒有結尾的空列
        'requiredField' => array()
    );

    /**
     * 標題定義
     *
     * @var array
     */
    protected $_title = array();

    /**
     * 內容定義
     *
     * @var array
     */
    protected $_content = array();

    /**
     * 結尾定義
     *
     * @var array
     */
    protected $_foot = array();

    /**
     * 對映表儲存表 - 下拉選單用
     *
     * $_listMap['目標鍵名'] = array(array('value' => '數值','text' => '數值名稱'),.....);
     *
     * @var array
     */
    protected $_listMap = array();

    /**
     * 暫存用數
     *
     * @var array
     */
    protected $_cache = array();

    /**
     * 資料範本 - 鍵值表及預設值
     *
     * 使用函式templateDefined()從內容定義中建構本表
     *
     * @var array
     */
    protected $_dataTemplate = array();

    /**
     * 喂給helper的欄位
     *
     * @var array
     */
    protected $_helperField = array(
        'key' => '',
        'value' => '',
        'col' => '1',
        'row' => '1',
        'skip' => '1'
    );

    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct()
    {
        // 初始化
        $this->initialize();
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
        $this->initialize();
    }

    /**
     * 初始化
     */
    public function initialize()
    {
        // ====== 初始化定義 ======
        $this->_title = array();
        $this->_content = array();
        $this->_foot = array();
        $this->_dataTemplate = array();
        
        $this->titleDefined();
        $this->contentDefined();
        $this->footDefined();
        // 必需在$this->contentDefined();之後
        $this->templateDefined();
        // ======
        
        // ====== 初始化對映表 ======
        $this->_listMap = array();
        $this->listMapInitialize();
        // ======
        
        return true;
    }

    /**
     * **********************************************
     * ************** Getting Function **************
     * **********************************************
     */
    
    /**
     * 取得設定檔參數
     *
     * @return array
     */
    public function getOption($optionName = null)
    {
        if (is_null($optionName)) {
            // 未定鍵名 - 取得全部
            return $this->_options;
        } else {
            // 指定鍵名
            if (! isset($this->_options[$optionName])) {
                throw new \Exception('Donot have option: ' . $optionName . ' !', 404);
            }
            
            return $this->_options[$optionName];
        }
    }

    /**
     * 取得標題定義
     *
     * @return array
     */
    public function getTitle()
    {
        if (empty($this->_title)) {
            $this->titleDefined();
        }
        
        return $this->_title;
    }

    /**
     * 取得內容定義
     *
     * @return array
     */
    public function getContent()
    {
        if (empty($this->_content)) {
            $this->contentDefined();
        }
        
        return $this->_content;
    }

    /**
     * 取得結尾定義
     *
     * @return array
     */
    public function getFoot()
    {
        if (empty($this->_foot)) {
            $this->footDefined();
        }
        
        return $this->_foot;
    }

    /**
     * 取得設定格式範本 - 鍵值表及預設值
     *
     * 如需動態設定預設值時，需取出本表修改後回寫
     *
     * 取得：無參數時
     * 設定：有參數時
     *
     * @return array
     */
    public function getConfTemplate()
    {
        if ($this->_options['type'] == 'complex') {
            // 模式：複雜(complex)
            $config = array(
                'config' => array(
                    'name' => '結構名稱',
                    'style' => array(
                        '結構自定樣式集'
                    ),
                    'class' => '結構自定樣式名'
                ),
                'defined' => array(
                    'key' => array(
                        'key' => '鍵名',
                        'value' => '(在結構設定中，此值為該欄位名稱)',
                        'desc' => '說明',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '預設值',
                        'list' => '下拉選單名'
                    )
                )
            );
        } else {
            // 模式：簡易(simple)
            $config = array(
                'u_no' => '編號',
                'c_name' => '姓名',
                'id_no' => '身分證字號',
                'birthday' => '出生年月日',
                'u_country' => '國別'
            );
        }
        
        return $config;
    }

    /**
     * 取得選單對映範本 - 鍵值表及預設值
     *
     * 如需動態設定預設值時，需取出本表修改後回寫
     *
     * 取得：無參數時
     * 設定：有參數時
     *
     * @return array
     */
    public function getListTemplate()
    {
        return array(
            '$key' => array(
                'value' => '數值',
                'text' => '數值名稱'
            )
        );
    }

    /**
     * 取得資料範本 - 鍵值表及預設值
     *
     * 如需動態設定預設值時，需取出本表修改後回寫
     *
     * 取得：無參數時
     * 設定：有參數時
     *
     * @return array
     */
    public function getDataTemplate()
    {
        if (empty($this->_dataTemplate)) {
            $this->templateDefined();
        }
        
        return $this->_dataTemplate;
    }

    /**
     * 取得對映表 - 下拉選單:值&文字
     *
     * @param string $key
     *            鍵名，不指定則傳回全部
     * @return array
     */
    public function &getList($key = null)
    {
        if (is_null($key)) {
            // 未定鍵名 - 取得全部
            return $this->_listMap;
        } else {
            // 指定鍵名
            if (! isset($this->_listMap[$key])) {
                throw new \Exception('List Map donot have key: ' . $key . ' !', 404);
            }
            return $this->_listMap[$key];
        }
    }

    /**
     * **********************************************
     * ************** Setting Function **************
     * **********************************************
     */
    
    /**
     * 設置設定檔參數
     *
     * @param array $option
     *            參數值
     * @param string $optionName
     *            參數名稱
     * @return \nueip\io\config\abstracts\Config
     */
    public function setOption($option, $optionName = null)
    {
        if (is_null($optionName)) {
            $this->_options = $option;
        } else {
            $this->_options[$optionName] = $option;
        }
        
        return $this;
    }

    /**
     * 設定標題定義
     *
     * 標題可有多個，以陣列儲存
     *
     * @param array $data
     *            格式定義
     * @return \nueip\io\config\abstracts\Config
     */
    public function setTitle($data = null)
    {
        if (! is_null($data) && is_array($data)) {
            $this->_title[] = $data;
        } else {
            // 沒有傳入值時
            $this->titleDefined();
        }
        
        return $this;
    }

    /**
     * 設定內容定義
     *
     * 內容只有一個，直接儲存
     *
     * @param array $data
     *            格式定義
     * @return \nueip\io\config\abstracts\Config
     */
    public function setContent($data = null)
    {
        if (! is_null($data) && is_array($data)) {
            $this->_content = $data;
            // 設定資料範本 - 鍵值表及預設值
            $this->templateDefined();
        } else {
            $this->contentDefined();
            // 設定資料範本 - 鍵值表及預設值
            $this->templateDefined();
        }
        
        return $this;
    }

    /**
     * 設定結尾定義
     *
     * 結尾可有多個，以陣列儲存
     *
     * @param array $data
     *            格式定義
     * @return \nueip\io\config\abstracts\Config
     */
    public function setFoot($data = null)
    {
        if (! is_null($data) && is_array($data)) {
            $this->_foot[] = $data;
        } else {
            $this->footDefined();
        }
        
        return $this;
    }

    /**
     * 設定資料範本 - 鍵值表及預設值
     *
     * 資料範本無法直接修改，如需動態設定預設值時，請修改內容設定後，再執行本函式
     *
     * @return array
     */
    public function setTemplate()
    {
        $this->templateDefined();
        
        return $this;
    }

    /**
     * 設定對映表 - 下拉選單:值&文字
     *
     * @param array $mapData
     *            對映表資料
     * @param string $key
     *            鍵名
     * @return \nueip\io\config\abstracts\Config
     */
    public function setList(Array $mapData, $key = null)
    {
        if (is_null($key)) {
            $this->_listMap = $mapData;
        } else {
            $this->_listMap[$key] = $mapData;
        }
        
        return $this;
    }

    /**
     * **********************************************
     * ************** Options Function **************
     * **********************************************
     */
    
    /**
     * 版本檢查
     *
     * @param string $version
     *            版本編號
     */
    public function checkVersion($version)
    {
        return $this->_options['version'] == $version;
    }

    /**
     * 參數編碼 - 結構定義物件內容
     *
     * 將結構定義物件內容編碼後待存入參數工作表中
     *
     * @return string[]
     */
    public function optionEncode()
    {
        return array(
            'ConfigContent',
            json_encode($this->_options),
            json_encode($this->_title),
            json_encode($this->_content),
            json_encode($this->_foot),
            json_encode($this->_listMap)
        );
    }

    /**
     * 參數解析 - 結構定義物件內容
     *
     * 解析來自參數工作表中讀到的參數 (依序還原Key)
     * 為本設定檔資料時，才回傳解析後的資料，否則回傳false
     *
     * @param array $optionData
     *            參數
     * @return boolean|mixed
     */
    public function optionDecode(Array $optionData)
    {
        // 依序還原Key
        
        // 驗証資料
        $opt = false;
        if ($optionData[0] == 'ConfigContent') {
            $options = json_decode($optionData[1], 1);
            // 版本支援
            if ($options['version'] >= $this->_options['minVersion']) {
                $opt = $options;
                $this->_options = $options;
                $this->_title = json_decode($optionData[2], 1);
                $this->_content = json_decode($optionData[3], 1);
                $this->_foot = json_decode($optionData[4], 1);
                $this->_listMap = json_decode($optionData[5], 1);
                // 設定資料範本 - 鍵值表及預設值
                $this->templateDefined();
            }
        }
        
        return $opt;
    }

    /**
     * ******************************************************
     * ************** Content Process Function **************
     * ******************************************************
     */
    
    /**
     * 內容整併 - 以資料內容範本為模版合併資料
     *
     * 整併原始資料後，如需要多餘資料執行額外處理，可在處理完後再執行內容過濾 $this->contentFilter($data);
     *
     * @param array $data
     *            原始資料內容
     * @return \nueip\io\config\AddInsConfig
     */
    public function contentRefactor(Array & $data)
    {
        // 將現有對映表轉成value=>text格式存入暫存
        $this->value2TextMapBuilder();
        
        foreach ($data as $key => &$row) {
            $row = (array) $row;
            
            // 以資料內容範本為模版合併資料
            $row = array_merge($this->_dataTemplate, $row);
            
            // 內容整併處理時執行 - 迴圈內自定步驟
            $this->eachRefactor($key, $row);
            
            // 執行資料轉換 value <=> text - 單筆資料
            $this->valueTextConv($key, $row);
        }
        
        return $this;
    }

    /**
     * 內容過濾 - 以資料內容範本為模版過濾多餘資料
     *
     * 將不需要的多餘資料濾除，通常處理$this->contentRefactor($data)整併完的內容
     *
     * @param array $data
     *            原始資料內容
     * @return \nueip\io\config\AddInsConfig
     */
    public function contentFilter(Array & $data)
    {
        foreach ($data as $key => &$row) {
            $row = (array) $row;
            
            // 以資料內容範本為模版過濾多餘資料 - 有設定才過濾
            if (! empty($this->_dataTemplate)) {
                $row = array_intersect_key($row, $this->_dataTemplate);
            }
        }
        
        return $this;
    }

    /**
     * 匯入資料解析
     *
     * 將匯入的資料依資料範本給key，並做資料轉換 text=>value
     *
     * @param array $data
     *            匯入的原始資料
     * @return \nueip\io\config\abstracts\Config
     */
    public function contentParser(Array & $data)
    {
        // 將現有對映表轉成text=>value格式存入暫存
        $this->text2ValueMapBuilder();
        // 資料範本資料量、key
        $templateSize = sizeof($this->_dataTemplate);
        $templateKey = array_keys($this->_dataTemplate);
        
        // 有資料範本時，才解析
        if ($templateSize) {
            // 遍歷資料，並解析
            foreach ($data as $key => &$row) {
                $row = (array) $row;
                
                $row = array_slice($row, 0, $templateSize);
                $row = array_combine($templateKey, $row);
                
                // 執行資料轉換 value <=> text - 單筆資料
                $this->valueTextConv($key, $row);
            }
        }
        
        return $this;
    }

    /**
     * 匯入資料驗証 - 必要欄位驗証
     *
     * 使用必要欄位驗証：有設定必要欄位，且該欄位有無資料的狀況，跳出，因各版本excel對空列定義不同，可能編輯過列，就會產生沒有結尾的空列
     *
     * @param array $row
     *            單列資料
     */
    public function contentValidate(Array $row)
    {
        // 取得必要欄位
        $requiredField = $this->getOption('requiredField');
        $requiredField = is_array($requiredField) ? $requiredField : array();
        // 資料範本資料量、key
        $templateSize = sizeof($this->_dataTemplate);
        $templateKeyFlip = array_flip(array_keys($this->_dataTemplate));
        
        $opt = true;
        
        // 有資料範本資料、設定必要欄位才驗証
        if ($templateSize && ! empty($requiredField)) {
            foreach ($requiredField as $keyCode) {
                // 列資料$row有$requiredField指定的欄位，且該欄無資料時，回傳false
                if (isset($templateKeyFlip[$keyCode]) && isset($row[$templateKeyFlip[$keyCode]]) && empty($row[$templateKeyFlip[$keyCode]])) {
                    return false;
                }
            }
        }
        
        return $opt;
    }

    /**
     * 執行資料轉換 value <=> text - 單筆資料
     *
     * 依照建構的對映表是value => text，還是text =>value決定轉換方向
     * 不管同名(text)問題
     *
     * @param string $key
     *            當次迴圈的Key值
     * @param array $row
     *            當次迴圈的內容
     */
    public function valueTextConv($key, &$row)
    {
        // 遍歷資料，並轉換內容
        foreach ($row as $k => &$v) {
            // 檢查是否需要內容轉換
            if (! isset($this->_cache['valueTextMap'][$k])) {
                continue;
            }
            
            // 處理資料轉換
            $v = isset($this->_cache['valueTextMap'][$k][$v]) ? $this->_cache['valueTextMap'][$k][$v] : '';
        }
    }

    /**
     * **************************************************
     * ************** Map Builder Function **************
     * **************************************************
     */
    
    /**
     * 將現有對映表轉成value=>text格式存入暫存
     */
    public function value2TextMapBuilder()
    {
        // 初始化暫存
        $this->_cache['valueTextMap'] = array();
        
        foreach ($this->_listMap as $key => $map) {
            $this->_cache['valueTextMap'][$key] = array_column($map, 'text', 'value');
        }
    }

    /**
     * 將現有對映表轉成text=>value格式存入暫存
     */
    public function text2ValueMapBuilder()
    {
        // 初始化暫存
        $this->_cache['valueTextMap'] = array();
        
        foreach ($this->_listMap as $key => $map) {
            $this->_cache['valueTextMap'][$key] = array_column($map, 'value', 'text');
        }
    }

    /**
     * **********************************************
     * ************** Defined Function **************
     * **********************************************
     */
    
    /**
     * 資料範本 - 鍵值表及預設值
     *
     * 從內容定義撈取
     */
    protected function templateDefined()
    {
        $content = $this->getContent();
        $defined = isset($content['defined']) ? $content['defined'] : (array) $content;
        $template = array();
        
        foreach ($defined as $key => $info) {
            if (is_array($info)) {
                // 模式：複雜
                $template[$key] = isset($info['default']) ? $info['default'] : '';
            } else {
                // 模式：簡易
                $template[$key] = $info;
            }
        }
        
        $this->_dataTemplate = $template;
    }

    /**
     * 設定資料過濾，在喂給helper時不會有多餘的資料
     *
     * @param array $defined            
     */
    public function definedFilter(& $defined)
    {
        if (isset($defined['defined'])) {
            // 模式：複雜(complex)
            $def = & $defined['defined'];
            
            foreach ($def as $key => $info) {
                $def[$key] = array_intersect_key(array_merge($this->_helperField, $info), $this->_helperField);
            }
        }
        
        return $defined;
    }

    /**
     * 從定義資料中取得列定義
     *
     * 有$defined['defined']為複雜(complex)模式，否則為簡易(simple)模式
     *
     * @param array $defined            
     * @return array
     */
    public function getRowFromDefined($defined)
    {
        return isset($defined['defined']) ? $defined['defined'] : $defined;
    }

    /**
     * **********************************************
     * ************** Abstract Function **************
     * **********************************************
     */
    
    /**
     * 內容整併處理時執行 - 迴圈內自定步驟
     *
     * 對內容整併時，需額外處理的欄位，不建議使用，應在原始資料傳入時就做好
     *
     * @param string $key
     *            當次迴圈的Key值
     * @param array $row
     *            當次迴圈的內容
     */
    protected abstract function eachRefactor($key, &$row);

    /**
     * 初始化對映表
     */
    protected abstract function listMapInitialize();

    /**
     * 標題定義函式範例
     *
     * // 結構一 - 標題1
     * $this->_title[] = array(
     * 'config' => array(
     * 'type' => 'title',
     * 'name' => 'title1',
     * 'style' => array(
     * 'font-size' => '16'
     * ),
     * 'class' => 'title1'
     * ),
     * 'defined' => array(
     * 't1' => array(
     * 'key' => 't1',
     * 'value' => get_language('id'), //'員工編號',
     * 'col' => '1',
     * 'row' => '1',
     * 'style' => array(),
     * 'class' => '',
     * 'default' => '',
     * 'list' => ''
     * ),
     * 't2' => array(
     * 'key' => 't2',
     * 'value' => get_language('name'), //'姓名',
     * 'col' => '1',
     * 'row' => '1',
     * 'style' => array(),
     * 'class' => '',
     * 'default' => '',
     * 'list' => ''
     * )
     * )
     * );
     *
     * 結構二 - 標題1
     * $this->_title[] = array(
     * 'config' => array(
     * 'type' => '結構名稱',
     * 'name' => '結構名稱',
     * 'style' => array('結構自定樣式集'),
     * 'class' => '結構自定樣式名',
     * ),
     * 'defined' => array(
     * 'u_no' => '編號',
     * 'c_name' => '姓名',
     * 'id_no' => '身分證字號',
     * 'birthday' => '出生年月日',
     * 'u_country' => '國別',
     * :
     * :
     * )
     * );
     *
     * 單一標題定義可擁有單列資料，所以可定義多個標題定義
     */
    protected abstract function titleDefined();

    /**
     * 內容定義函式
     *
     * 單一內容定義可擁有多列資料，所以只能有一個內容定義
     */
    protected abstract function contentDefined();

    /**
     * 結尾定義函式
     *
     * 單一結尾定義可擁有單列資料，所以可定義多個結尾定義
     */
    protected abstract function footDefined();
}

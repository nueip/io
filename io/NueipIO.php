<?php
namespace app\libraries\io;

/**
 * NuEIP IO Library
 *
 * 單一工作表IO
 *
 * @example 
 *          $this->load->library('tw_ins_management/Tw_ins_management_component');
 *         
 *          // get all data
 *          $data = $this->tw_ins_management_component->empList([
 *          'select_type' => 'all',
 *          'iu_sn' => ''
 *          ]);
 *         
 *          $io = new \app\libraries\io\NueipIO();
 *          $io->export($data, $config = 'AddIns', $builder = 'Excel', $style = 'Nueip');
 *         
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class NueipIO
{
    /**
     * 預設參數
     * @var array
     */
    protected $_options = array(
        'fileName' => 'export'
    );
    
    /**
     * 資料
     * @var array
     */
    protected $_data = array();
    
    /**
     * 定義資料
     * @var array
     */
    protected $_config = array();
    
    /**
     * Style定義資料
     * @var array
     */
    protected $_style = array();
    
    /**
     * 下拉選單定義資料
     *
     * @var array $list[$key] = array('value' => '值', 'text' => '文字', 'type' => '資料類型');
     */
    protected $_listMap = array();
    
    /**
     * 建構函式
     * @var object
     */
    protected $_builder;
    
    /**
     * Construct
     *
     * @throws Exception
     */
    public function __construct(Array $options = array())
    {
        // 初始化參數
        $this->_options = array_intersect_key(array_merge($this->_options, $options), $this->_options);
        
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
    
    /**
     * 匯出
     */
    public function export($data, $config, $builder = 'Excel', $style = 'Nueip')
    {
        // 載入資料
        $this->setData($data);
        
        // 載入定義檔
        $this->setConfig($config);
        
        // 建立io物件
        $this->setBuilder($builder);
        
        // 載入Style定義
        $this->setStyle($style);
        
        // 匯出建構並輸出
        $this->buildExport();
    }

    /**
     * 匯入
     */
    public function import($config, $builder = 'Excel')
    {
        // 取得上傳資料 - 上傳檔轉資料陣列
        $row = $this->uploadFile2Raw();
        
        // 載入資料
        $this->setData($row);
        
        // 載入定義檔
        $this->setConfig($config);
        
        // 建立io物件
        $this->setBuilder($builder);
        
        // 匯入建構並回傳
        $this->buildImport();
        exit;
        
        // 取得資料陣列
        return $this->_builder->output();
    }
    
    /**
     * 參數設定
     *
     * @param string $opName 參數名稱
     * @param string $opValue 參數值
     * @return \app\libraries\io\NueipIO
     */
    public function setOption($opName, $opValue)
    {
        $this->_options[$opName] = $opValue;
        return $this;
    }
    
    /**
     * 載入資料
     *
     * @param string $data 資料
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }
    
    /**
     * 載入定義檔
     *
     * @param string $config 定義檔
     */
    public function setConfig($config)
    {
        $this->_config = \app\libraries\io\ConfigFactory::get($config);
        return $this;
    }
    
    /**
     * 載入Style定義
     *
     * @param string $style IO物件
     */
    public function setStyle($style = 'Nueip')
    {
        $this->_style = \app\libraries\io\StyleFactory::get($style);
        return $this;
    }
    
    /**
     * 載入下拉選單定義資料
     *
     * @param string $style IO物件
     */
    public function setList($keyName, $listDEfined)
    {
        $this->_listMap[$keyName] = $listDEfined;
        return $this;
    }
    
    /**
     * 建立io物件
     *
     * @param string $builder IO物件
     */
    public function setBuilder($builder = 'excel')
    {
        $this->_builder = \app\libraries\io\BuilderFactory::get($builder);
        return $this;
    }
    
    /**
     * 取得定義檔
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * ***********************************************
     * ************** Building Function **************
     * ***********************************************
     */
    
    /**
     * 匯出建構並輸出
     */
    public function buildExport()
    {
        // 載入參數
        $this->_builder->setOptions($this->_options);
        // 載入資料
        $this->_builder->setData($this->_data);
        // 載入結構定義
        $this->_builder->setConfig($this->_config);
        // 載入樣式定義
        $this->_builder->setStyle($this->_style);
        
        // 載入下拉選單定義 - 額外定義資料
        foreach ($this->_listMap as $keyName => $listDEfined) {
            $this->_builder->setList($keyName, $listDEfined);
        }
        
        // 建構資料 & 輸出
        $this->_builder->build()->output();
    }
    
    /**
     * 匯入建構並回傳
     */
    public function buildImport()
    {
        // 載入參數
        $this->_builder->setOptions($this->_options);
        // 載入資料
        $this->_builder->setData($this->_data);
        // 載入結構定義
        $this->_builder->setConfig($this->_config);
        
        // 載入下拉選單定義 - 額外定義資料
        foreach ($this->_listMap as $keyName => $listDEfined) {
            $this->_builder->setList($keyName, $listDEfined);
        }
        
        // 建構資料 & 輸出
        return $this->_builder->parse()->get();
    }
    
    /**
     * **********************************************
     * ************** Private Function **************
     * **********************************************
     */
    
    /**
     * 取得上傳資料 - 上傳檔轉資料陣列
     *
     * @throws Exception
     * @return array
     */
    protected function uploadFile2Raw()
    {
        // 上傳路徑
        $UploadDir = 'uploads/tmp_files/';
        if (! is_dir($UploadDir)) {
            mkdir($UploadDir, 0700);
        }
        
        // 錯誤檢查
        if (! isset($_FILES['fileupload'])) {
            throw new Exception('File upload failed !', 400);
        }
        
        // 檔案資料
        $UploadFile_t = $_FILES['fileupload']['name'];
        $UploadFile = substr($UploadFile_t, 0, strrpos($UploadFile_t, '.')); // 取得檔案名稱
        $sub_name = substr($UploadFile_t, strrpos($UploadFile_t, '.') + 1, strlen($UploadFile_t) - strlen($UploadFile) + 1); // 取得副檔名名稱
        $uploadfile = $UploadDir . basename(md5($UploadFile) . '.' . $sub_name);
        
        // 檔案檢查 - 副檔名
        if ($sub_name != 'xls' && $sub_name != 'xlsx') {
            throw new Exception(get_language('wrongtype'), 400);
        }
        
        // 處理上傳檔案
        $data = array();
        if (move_uploaded_file($_FILES['fileupload']['tmp_name'], $uploadfile)) {
            // 載入上傳檔案至PHPExcel
            $helper = \yidas\phpSpreadsheet\Helper::newSpreadsheet($uploadfile);
            unlink($uploadfile);
            
            // 取得原始資料
            while ($row = $helper->getRow()) {
                $data[] = $row;
            }
            
            // 取得參數資料 - 設定檔參數
            $helper->getSheet('ConfigSheet');
            $config1 = $helper->getRow();
            
            // 取得參數資料 - 建構函式參數
            $config2 = $helper->getRow();
            
            var_export($config1);
            
            exit;
        } else {
            throw new Exception('File Upload Failure !', 400);
        }
        
        return $data;
    }
    
    
}

<?php
namespace app\libraries\io2;

/**
 * NuEIP IO Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class NueipIO
{
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
    protected $_list = array();
    
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
    public function __construct()
    {
        // 初始化
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
        // 建立io物件
        
        // 載入資料
        $this->setData($data);
        
        // 載入定義檔
        $this->setConfig($config);
        
        // 建立io物件
        $this->setBuilder($builder);
        
        // 載入Style定義
        $this->setStyle($style);
        
        // 匯出
        $this->buildExport();
    }

    /**
     * 匯入
     */
    public function import()
    {
        // 取得上傳資料 - 上傳檔轉資料陣列
        $row = $this->uploadFile2Row();
        
        // 載入資料
        $this->setData($row);
        
        // 載入定義檔
        $this->setConfig($config);
        
        // 建立io物件
        $this->setBuilder($builder);
        
        // 取得資料陣列
        return $this->_builder->output();
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
        $this->_config = \app\libraries\io2\ConfigFactory::get($config);
        return $this;
    }
    
    /**
     * 載入Style定義
     *
     * @param string $style IO物件
     */
    public function setStyle($style = 'Nueip')
    {
        $this->_style = \app\libraries\io2\StyleFactory::get($style);
        return $this;
    }
    
    /**
     * 載入下拉選單定義資料
     *
     * @param string $style IO物件
     */
    public function setList($list)
    {
        $this->_list = $list;
        return $this;
    }
    
    /**
     * 建立io物件
     *
     * @param string $builder IO物件
     */
    public function setBuilder($builder = 'excel')
    {
        $this->_builder = \app\libraries\io2\BuilderFactory::get($builder);
        return $this;
    }
    
    /**
     * ***********************************************
     * ************** Building Function **************
     * ***********************************************
     */
    
    /**
     * 建構並輸出
     */
    public function buildExport()
    {
        $this->_builder->setData($this->_data);
        $this->_builder->setConfig($this->_config);
        $this->_builder->setStyle($this->_style);
        $this->_builder->setList($this->_list);
        
        $this->_builder->build()->output();
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
    private function uploadFile2Row()
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
            
            // 取得資料
            while ($row = $helper->getRow()) {
                $data[] = $row;
            }
        } else {
            throw new Exception('File Upload Failure !', 400);
        }
        
        return $data;
    }
}

<?php
namespace app\libraries\io2;

/**
 * NuEIP IO Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class ConfigFactory
{
    
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
     * ****************************************************
     * ************** Public Static Function **************
     * ****************************************************
     */
    
    /**
     * 建構函式 - 工廠模式
     * 
     * @param string $category 設定檔類別名稱
     * @return object
     */
    public static function get($name)
    {
        $class = '\\'.__NAMESPACE__.'\\config\\'.$name.'Config';
        return new $class();
    }
}

<?php
namespace app\libraries\io;

/**
 * NuEIP IO Library
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class BuilderFactory
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
     * @param string $category 建構函式類別名稱
     * @return object
     */
    public static function get($name)
    {
        // 初炲化建構物件
        $class = '\\'.__NAMESPACE__.'\\builder\\'.$name.'Builder';
        return new $class();
    }
}

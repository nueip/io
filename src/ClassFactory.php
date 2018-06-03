<?php
namespace marshung\io;

/**
 * 初始化工廠
 *
 * 提供物件初始化
 * 1. 格式處理總成物件
 * 2. 結構定義物件
 * 3. 樣式定義物件
 *
 * @author Mars.Hung (tfaredxj@gmail.com) 2018-04-14
 *        
 */
class ClassFactory
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
     * 格式處理總成物件 - 建構函式 - 工廠模式
     *
     * @param string $class
     *            建構函式類別名稱
     * @return object
     */
    public static function getBuilder($class)
    {
        // 初炲化建構物件
        return self::_newClass($class, 'builder');
    }

    /**
     * 結構定義物件 - 建構函式 - 工廠模式
     *
     * @param string $class
     *            建構函式類別名稱
     * @return object
     */
    public static function getConfig($class)
    {
        // 初炲化建構物件
        return self::_newClass($class, 'config');
    }

    /**
     * 樣式定義物件 - 建構函式 - 工廠模式
     *
     * @param string $class
     *            建構函式類別名稱
     * @return object
     */
    public static function getStyle($class)
    {
        // 初炲化建構物件
        return self::_newClass($class, 'style');
    }

    /**
     * ****************************************************
     * ************** Public Static Function **************
     * ****************************************************
     */
    
    /**
     * 建構函式 - 工廠模式
     *
     * @param string $class
     *            建構函式類別名稱
     * @return object
     */
    protected static function _newClass($class, $type)
    {
        // 傳入的class名稱不帶名稱空間時，加上名稱空間
        if (strpos($class, '\\') === false) {
            switch ($type) {
                case 'builder':
                    $class = '\\' . __NAMESPACE__ . '\\builder\\' . $class . 'Builder';
                    break;
                case 'config':
                    $class = '\\' . __NAMESPACE__ . '\\config\\' . $class . 'Config';
                    break;
                case 'style':
                    $class = '\\' . __NAMESPACE__ . '\\style\\' . $class . 'Style';
                    break;
                default:
                    throw new \Exception('Class Type (' . var_export($type, 1) . ') Not Found !', 400);
                    break;
            }
        }
        
        return new $class();
    }
}

<?php
/**
 * Class DomainModelCache
 * @package Core
 * @subpackage Library
 */

/**
 * Cache registry object for DomainModel instances
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class DomainModelCache
{

    /**
     * Registry of instances
     * @var array
     */
    private static $_instances = array();
    
    /** **/
    
    /**
     * Stores a DomainModel in the cache
     * @param DomainModel $model
     */
    public static function store(DomainModel $model)
    {
        $className = get_class($model);
        
        if(!isset(self::$_instances[$className])) {
            self::$_instances[$className] = array();
        }
        
        $identifier = $model->getIdentifier();
        if(!empty($identifier)) {
            self::$_instances[$className][$identifier] = $model;
        }
        
    }
    
    /**
     * Fetches a model from the cache by its class name and identifier.
     * @param string $className
     * @param int $identifier
     * @return DomainModel|boolean
     */
    public static function fetch($className, $identifier)
    {
        if(self::check($className, $identifier)) {
            return self::$_instances[$className][$identifier];
        }
        else {
            return false;
        }
    }
    
    /**
     * Check if a model class with an identifier exists in the cache.
     * @param string $className
     * @param string $identifier
     * @return boolean
     */
    public function check($className, $identifier)
    {
        return isset(self::$_instances[$className][$identifier]);
    }
    
    /**
     * Removes an instance from the cache
     * @param string $className
     * @param string $identifier
     */
    public function remove($className, $identifier)
    {
        if(self::check($className, $identifier)) {
            unset(self::$_instances[$className][$identifier]);
        }
    }
    
    /**
     * Returns a list of class names and their cached instance identifiers.
     * @return array
     */
    public function listCache()
    {
        $list = array();
        foreach(self::$_instances as $class => $instances) {
            $list[$class] = array_keys($instances);
        }
        
        return $list;
    }
    
}

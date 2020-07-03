<?php

namespace Ramapriya\LoadManager;

use Bitrix\Main\Loader;

/**
 * @package Autoload
 * @author Ramapriya Doom
 */

class Autoload
{
    /**
     * scan lib directory
     * 
     * @param string $dir
     * 
     * @return array
     */
    
    public static function scanDirectory(string $dir) : array
    {
        $result = [];
        $scanner = scandir($dir);
        foreach ($scanner as $scan) {
            switch ($scan) {
                case '.':
                case '..':
                    break;
                default:
    
                    $item = $dir . '/' . $scan;
                    $SplFileInfo = new \SplFileInfo($item);
    
                    if($SplFileInfo->isFile()) {
    
                        $result[] = $scan;
                        
                    } elseif ($SplFileInfo->isDir()) {
                        
                        $result[$scan] = self::scanDirectory($item, $result[$scan]);
    
                    }
            }
        }
    
        return $result;
    }

    /**
     * Prepare array for autoload
     * 
     * @param string $directory
     * @param string $defaultNamespace
     * @param array $excludeFiles
     * 
     * @return array
     */

    public static function prepareAutoloadClassesArray(string $directory, string $defaultNamespace, array $excludeFiles) : array
    {
        $result = [];    
        $scanner = self::scanDirectory($directory);
    
        foreach ($scanner as $key => $value) {
    
            $sep = '\\';
            
            switch(gettype($key)) {
                
                case 'string':
    
                    $SplFileInfo = new \SplFileInfo($directory . '/' . $key);
                    $classNamespace = $defaultNamespace . $sep . $key;
    
                    if($SplFileInfo->isDir()) {
                        $tempResult = self::prepareAutoloadClassesArray($directory . '/' . $key, $classNamespace, $excludeFiles);
                        foreach($tempResult as $class => $file) {
                            $result[$class] = $file;
                        }
                    }
    
                    break;
    
                case 'integer':
    
                    $SplFileInfo = new \SplFileInfo($directory . '/' . $value);
                    $classNamespace = $defaultNamespace . $sep . str_ireplace('.php', '', $SplFileInfo->getBasename());
    
                    if(
                        $SplFileInfo->isFile() &&
                        $SplFileInfo->getExtension() === 'php'
                    ) {
                        
                        foreach($excludeFiles as $excludeFile) {
                            if($SplFileInfo->getBasename() !== $excludeFile) {
                                $result[$classNamespace] = str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $directory . '/' . $value);
                            }
                        }
                        
                        
                    }
    
                    break;
                    
            }
    
        }
    
        return $result;
    }

    /**
     * Load prepared classes from array
     * 
     * @param array $classes
     * @param $moduleId
     */

    public static function loadClasses(array $classes, $moduleId = null)
    {
        Loader::registerAutoloadClasses($moduleId, $classes);
    }

}
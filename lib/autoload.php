<?php

namespace Ramapriya\LoadManager;

use Bitrix\Main\Loader;

class Autoload
{
    
    public static function scanDirectory($dir, $folders = [])
    {
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
    
                        $folders[] = $scan;
                        
                    } elseif ($SplFileInfo->isDir()) {
                        
                        $folders[$scan] = self::scanDirectory($item, $folders[$scan]);
    
                    }
            }
        }
    
        return $folders;
    }

    public static function setAutoloadClassesArray(string $directory, string $defaultNamespace, array $excludeFiles)
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
                        $tempResult = self::setAutoloadClassesArray($directory . '/' . $key, $classNamespace, $excludeFiles);
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

    public static function loadClasses($classes, $moduleId = null)
    {
        Loader::registerAutoloadClasses($moduleId, $classes);
    }

}
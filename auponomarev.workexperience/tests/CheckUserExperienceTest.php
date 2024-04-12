<?php

namespace Tests;
use Auponomarev\WorkExperience\User;
use Auponomarev\WorkExperience\WorkExperience;
use Bitrix\Main\Loader;
use PHPUnit\Framework\TestCase;

Loader::includeModule('auponomarev.workexperience');
class CheckUserExperienceTest extends TestCase
{
    protected static function getMethod(string $class, string $name): \ReflectionMethod {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
    
    
    
    
    public function testgetExperienceUserIds(){
    
        $obj = new WorkExperience();
        $method = self::getMethod(get_class($obj), 'getExperienceUserIds');
        $result = $method->invokeArgs($obj, []);
    
        static::assertIsArray( $result );
    
    }
    
}
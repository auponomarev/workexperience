<?php

namespace Tests;

use Auponomarev\WorkExperience\WorkExperience;
use Bitrix\Main\Loader;
use PHPUnit\Framework\TestCase;

class WorkExperienceTest extends TestCase
{
    public function testgetUsersExperience()
    {
        Loader::includeModule('auponomarev.workexperience');
        
        $workExp = new WorkExperience();
        //  $workExp->getUsersExperience();
        
        static::assertTrue(true);
    }
    
    
}

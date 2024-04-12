<?php

namespace Tests;
use Auponomarev\WorkExperience\User;
use Bitrix\Main\Loader;
use PHPUnit\Framework\TestCase;

Loader::includeModule('auponomarev.workexperience');
class UserTest extends TestCase
{
    public function testuser(){
    
        $user = new User(1);
        
        $this->assertStringContainsString('admin@dev.loc', $user->getEmail());
    }
    
}
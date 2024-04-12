<?php

namespace Tests;
use Bitrix\Main\Loader;
use PHPUnit\Framework\TestCase;
class MainTest extends TestCase
{
    public function testSay(){
        $this->assertStringContainsString('ok', 'ok');
    }
    
    public function testIblock()
    {
        Loader::includeModule('iblock');
        $arIblock = \Bitrix\Iblock\IblockTable::getList(array(
            'filter' => array('ID' => 16) // параметры фильтра
        ))->fetch();
        
        static::assertIsInt($arIblock);
    }
    
}
<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Auponomarev_WorkExperience extends CModule
{
    public $MODULE_ID = "auponomarev.workexperience";

    public $MODULE_NAME;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_PATH;

    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage("auponomarev.workexperience_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("auponomarev.workexperience_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("auponomarev.workexperience_MODULE_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("auponomarev.workexperience_MODULE_PARTNER_URL");
    }
    
    public function DoInstall()
    {
        $this->registerModule();
        $this->registerEvents();
        \CAgent::AddAgent("\\Auponomarev\\WorkExperience\\WorkExperience::agentUserExperience();", $this->MODULE_ID, "N", 86400);
        $iblockId = $this->addIblock();
        COption::SetOptionString($this->MODULE_ID, "Iblock_id", $iblockId);
    }
    
    public function registerModule(): void
    {
        RegisterModule($this->MODULE_ID);
    }
    
    public function registerEvents(): void
    {
    }
    
    public function DoUninstall()
    {
        \CAgent::RemoveAgent("\\Auponomarev\\WorkExperience\\WorkExperience::agentUserExperience();", $this->MODULE_ID);
        \COption::RemoveOption($this->MODULE_ID);
        $this->unregisterEvents();
        $this->unRegisterModule();
    }
    
    public function unRegisterModule()
    {
        UnRegisterModule($this->MODULE_ID);
    }
    
    public function unregisterEvents(): void
    {
    }
    
    protected function addIblock() {
        // запрос инфоблока с кодом news
        $arIblock = \Bitrix\Iblock\IblockTable::getList(array(
            'filter' => array('CODE' => 'auponomarevworkexperience4') // параметры фильтра
        ))->fetch();
        
        $id = 0;
        
        if($arIblock) {
            $id = $arIblock['ID'];
        } else {
            $ib = new CIBlock();
            $arFields = array(
                "IBLOCK_TYPE_ID" => 'bitrix_processes',
                "CODE" => 'auponomarevworkexperience4',
                "LID" => 's1',
                "NAME" => 'Юбилеи сотрудников',
                "ACTIVE" => 'Y',
                "SORT" => 100,
                "INDEX_ELEMENT" => "N",
                "WORKFLOW" => 'N',
                "BIZPROC" => 'Y',
                "VERSION" => 1,
                "GROUP_ID" => array(1 => "X", 2 => "R"),
            );
    
            $id = $ib->Add($arFields);
        }
    
        $this->addFields($id);
        
        return $id;
    }
    
    protected function addFields(int $iblockId)
    {
    
        $ibp = new CIBlockProperty;
        $arFields = array (
            'NAME' => 'Сотрудник',
            'ACTIVE' => 'Y',
            'SORT' => '20',
            'CODE' => 'SOTRUDNIK',
            'DEFAULT_VALUE' => NULL,
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => NULL,
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '1',
            'IBLOCK_ID' => $iblockId,
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => 'employee',
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        );
        $PropID = $ibp->Add($arFields);
        
        if($ibp->LAST_ERROR) {
            var_dump($ibp->LAST_ERROR); die();
        }
    
        $arFields =  array (
            'NAME' => 'Дата юбилея',
            'ACTIVE' => 'Y',
            'SORT' => '30',
            'CODE' => 'DATA_YUBILEYA',
            'DEFAULT_VALUE' => NULL,
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => NULL,
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '1',
            'IBLOCK_ID' => $iblockId,
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => 'Date',
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        );
    
        $PropID = $ibp->Add($arFields);
        
        if($ibp->LAST_ERROR) {
            var_dump($ibp->LAST_ERROR); die();
        }
        
        $arFields =  array (
            'NAME' => 'Юбилей, лет',
            'ACTIVE' => 'Y',
            'SORT' => '40',
            'CODE' => 'YUBILEY_LET',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'N',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => NULL,
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '1',
            'IBLOCK_ID' => $iblockId,
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => 'a:0:{}',
            'HINT' => '',
        );
        $PropID = $ibp->Add($arFields);
        if($ibp->LAST_ERROR) {
            var_dump($ibp->LAST_ERROR); die();
        }
    }
}
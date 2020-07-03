<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class ramapriya_loadmanager extends CModule
{
    const MODULE_ID = 'ramapriya.loadmanager';

    public function __construct()
    {

        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        $this->MODULE_ID = self::MODULE_ID;
        $this->MODULE_NAME = Loc::getMessage('LOAD_MANAGER_MODULE_NAME');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->PARTNER_NAME = Loc::getMessage('LOAD_MANAGER_PARTNER_NAME');        

    }

    public function isD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    public function DoInstall()
    {
        if(!$this->isD7()) {
            throw new \Exception(Loc::getMessage('LOAD_MANAGER_INSTALL_ERROR_VERSION'));            
        }

        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
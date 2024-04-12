<?php

namespace Auponomarev\WorkExperience;

use Bitrix\Main\UserTable;

/**
 * Пользователь из базы данных
 */
final class User
{
    protected array $user = [];
    
    /**
     *
     * @param int $id
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(
        protected int $id,
    )
    {
        $this->user = UserTable::getById($id)->fetch();
    }
    
    
    /**
     * Почта пользователя.
     * @return string
     */
    public function getEmail(): string
    {
        return $this->user['EMAIL'] ?? '';
    }
    
    /**
     * @return string
     */
    public function getFullName(): string
    {
        $fio = [];
        if ($this->user['LAST_NAME']) {
            $fio[] = $this->user['LAST_NAME'];
        }
    
        if ($this->user['NAME']) {
            $fio[] = $this->user['NAME'];
        }
        
        return join(' ', $fio);
        
    }
    
    /**
     * @return int
     */
    public function getId():  int
    {
        return (int)$this->user['ID'];
    }
    
    
    
    
    
}
<?php

namespace Auponomarev\WorkExperience;

use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use CEvent;
use COption;

/**
 *
 * 1. Есть в профиле сотрудника поле «Стаж» (строка) в котором записывается автоматически величина стажа.
 * - Пример – «12 лет, 6 месяцев и 16 дней».
 * - Нужно написать агента, который проверял бы раз в месяц (20 числа) стаж всех сотрудников,
 * - на предмет наступления юбилея работы в компании на следующий месяц
 * - и создавал бы по каждому из пользователей, попадающих в выборку,
 * - элемент списка с заполнением значений, описанных в п.4.
 *
 * 2.	Юбилей компании – это 1 год, 5 лет, 10 лет и далее кратно 5 годам стажа.
 *
 * 3.	Список юбиляров должен содержать поля:
 * - Название,
 * - Дата создания,
 * - Сотрудник (привязка к пользователю),
 * - Дата юбилея (дата – записываем точную дату наступления юбилея),
 * - Юбилей, лет (целое число – сюда записываем стаж на момент наступления юбилея).
 *
 * 4.	За 3 дня до наступления юбилея ответственному сотруднику должно приходить
 * - уведомление на почту о том,
 * - что у сотрудника скоро юбилей – нужно составить поздравление.
 *
 */
final class WorkExperience
{
    const moduleId = 'auponomarev.workexperience';
    
    /**
     * Добавить список юбиляров.
     * @return void
     */
    public function getUsersExperience()
    {
        $users = $this->getUsers();
        $this->writeUsers($users);
    }
    
    /**
     * Отправить ответственному лицу список юбиляров.
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function checkUserExperience()
    {
        $userIds = $this->getExperienceUserIds();
        foreach ($userIds as $userId) {
            $this->sendEmail((int)$userId);
        }
    }
    
    public static function agentUserExperience()
    {
        $checkDay = COption::GetOptionInt(self::moduleId, "checkDay", 20);
        $todayDay = (int)(new DateTime())->format('d');
        
        $WorkExperience = new WorkExperience();
        
        if($checkDay === $todayDay) {
            $WorkExperience->getUsersExperience();
        }
    
        $WorkExperience->checkUserExperience();
        
        return "\\Auponomarev\\WorkExperience\\WorkExperience::agentUserExperience();";
    }
    
    /**
     * @return ExperianceUser[]
     */
    protected function getUsers(): array
    {
        $workExperienceFieldId = COption::GetOptionString(self::moduleId, "workexperiencefieldid", '-1');
        $checkDays = COption::GetOptionString(self::moduleId, "beforeDays", '-1');
        $allUsers = UserTable::getList([
            'select' => ['*', $workExperienceFieldId],
            'filter' => ['ACTIVE' => 'Y',],
        ])->fetchAll();
        
        $ExpUsers = [];
        foreach($allUsers as $user) {
            $res = $this->checkExperienceDate($user[$workExperienceFieldId], $checkDays, $user);
            if ($res) {
                $ExpUsers[] =  $res;
            }
        }
        
        return $ExpUsers;
    }
    
    /**
     * @param ExperianceUser[] $users
     * @return void
     */
    
    protected function writeUsers(array $users): void
    {
        foreach ($users as $user) {
            $this->addElement($user);
        }
    }
    
    /**
     * @param ExperianceUser $user
     * @return bool
     */
    protected function addElement(ExperianceUser $user): int
    {
        $iblockId = 16;
        $iblock = new \CIBlockElement();
        
        $fields = [
            'IBLOCK_ID' => $iblockId,
            'NAME' => $user->getTitle(),
            'PROPERTY_VALUES' => [
                'SOTRUDNIK' => $user->getId(),
                'DATA_YUBILEYA' => $user->getExperienceDate()->format('d.m.Y'),
                'YUBILEY_LET' => $user->getAnniversary(),
            ],
        ];
    
        $result = $iblock->Add($fields);
        if ($iblock->LAST_ERROR) {
            throw new \Exception($iblock->LAST_ERROR);
        }
        return $result;
    }
    
    /**
     *  Отправить email пользователю.
     *
     * @param int $experienceUserId
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function sendEmail(int $experienceUserId): void
    {
        $assignUser = COption::GetOptionInt(self::moduleId, "assignUser", 1);
        
        if ($assignUser && $experienceUserId) {
            $user = new User($assignUser);
            $experienceUser = new User($experienceUserId);
            
            $SITE_ID = 's1';
            $arFeedForm = [
                'EMAIL_TO' => $user->getEmail(),
                'SITE_NAME' => 'test',
                'ENTITY' => '',
                'ENTITY_TYPE' => '',
                'SUBSCRIBER_NAME' => $user->getFullName(),
                'TITLE' => 'У сотрудника скоро юбилей!!!',
                'MESSAGE' => "У сотрудника ({$experienceUser->getId()}){$experienceUser->getFullName()} скоро юбилей – нужно составить поздравление.",
                'URL' => '',
            ];
            CEvent::Send('SONET_NEW_EVENT', $SITE_ID, $arFeedForm);
        }
    }
    
    /**
     * Массив id сотрудников у кого юбилеи
     * @return int[]
     * @throws \Bitrix\Main\LoaderException
     */
    protected function getExperienceUserIds(): array
    {
        Loader::includeModule('iblock');
        $iblock_id = COption::GetOptionInt(self::moduleId, "Iblock_id", 16);
        $beforeDays = COption::GetOptionString(self::moduleId, "beforeDays", '-1');
        
        $beforeDaysDate = (new \DateTimeImmutable())->modify("{$beforeDays} days")->format('d.m.Y');
        
        $arFilter = [
            'IBLOCK_ID' => $iblock_id,
            'PROREPTY_DATA_YUBILEYA' => $beforeDaysDate,
        ];
    
        
        $rsUsers = \CIBlockElement::GetList(
            ['ID' => 'ASC'],
            $arFilter,
            false,
            false,
            ['ID', 'NAME', 'PROREPTY_SOTRUDNIK', 'PROREPTY_DATA_YUBILEYA']
        );
        $ids = [];
        while ($arElem = $rsUsers->Fetch()) {
            var_dump($arElem);
            $ids[] = $arElem['PROREPTY_SOTRUDNIK_VALUE'];
        }
        return $ids;
    }
    
    /**
     * Является ли сотрудник юбиляром
     * @param string $staj
     * @param int $checkDay
     * @param array $dbUser
     * @return ExperianceUser|bool
     */
    public function checkExperienceDate( string $staj, int $checkDay, array $dbUser): ExperianceUser|bool
    {
        if (preg_match('/\b\d+\b/ui', $staj, $match)) {
            $Y = $match[1];
            $m = $match[2];
            $d = $match[3];
            
            $lastDayMonth = (new DateTime())->format('t');
            $diffDays = $lastDayMonth - $checkDay + 1;
            
            $userDate = (new \DateTimeImmutable("{$Y}.{$m}.{$d}"));
            $userDateInt = $userDate->modify("$diffDays days")->format('Ymd');
            
            $nextMonth = (new \DateTimeImmutable())->modify("1 month");
            $nextMonthFirstDay = $nextMonth->format('Ym01');
            $nextMonthLastDay = $nextMonth->format('Ymt');
            
            
            if ($userDateInt >= $nextMonthFirstDay && $userDateInt <= $nextMonthLastDay) {
    
                $anniversary = 0;
                
                if ((int)$Y === 0) {
                    $anniversary = 1; //год стажа
                }
    
                if ((int)$Y % 5 === 0) {
                    $anniversary = intdiv((int)$Y, 5); //года стажа
                }
                
                if ($anniversary > 0) {
                    return new  ExperianceUser(
                        $dbUser['ID'],
                        $anniversary,
                        $userDate->format('Y.m.d'),
                        "{$dbUser['LAST_NAME']} {$dbUser['NAME']}"
                    );
                }
            }
        }
        
        return false;
    }
    
}

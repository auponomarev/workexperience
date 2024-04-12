<?php

namespace Auponomarev\WorkExperience;

use Bitrix\Main\Type\DateTime;

/**
 * Юбиляр
 */
class ExperianceUser
{
    public function __construct(
        protected int $id,
        protected string $anniversary,
        protected string $experienceDate,
        protected string $title
    )
    {
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getExperienceDate(): DateTime
    {
        return new DateTime($this->experienceDate);
    }
    
    public function getAnniversary(): int
    {
        return $this->anniversary;
    }
    
}
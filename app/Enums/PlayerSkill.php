<?php

namespace App\Enums;

enum PlayerSkill:string{
    case DEFENSE = 'defense';
    case ATTACK = 'attack';
    case SPEED = 'speed';
    case STRENGTH = 'strength';
    case STAMINA = 'stamina';

    public static function getValue($value)
    {
        return match ($value) {
            'DEFENSE' => PlayerSkill::DEFENSE,
            'ATTACK' => PlayerSkill::ATTACK,
            'SPEED' => PlayerSkill::SPEED,
            'STRENGTH' => PlayerSkill::STRENGTH,
            'STAMINA' => PlayerSkill::STAMINA,
        };
    }
}
<?php

namespace App\Enums;

enum PlayerPosition:string{
    case DEFENDER = 'defender';
    case MIDFIELDER = 'midfielder';
    case FORWARD = 'forward';

    public static function getValue($value)
    {
        return match ($value) {
            'DEFENDER' => PlayerPosition::DEFENDER,
            'MIDFIELDER' => PlayerPosition::MIDFIELDER,
            'FORWARD' => PlayerPosition::FORWARD
        };
    }
}
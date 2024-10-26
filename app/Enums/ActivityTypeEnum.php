<?php

namespace App\Enums;

enum ActivityTypeEnum: string
{
    case CREATE_CARD = 'CREATE_CARD';
    case PRACTICE_CARD = 'PRACTICE_CARD';
    case PRACTICE_ALL_CARDS = 'PRACTICE_ALL_CARDS';
    case QUIZ = 'QUIZ';
    case MATCHING = 'MATCHING';
}

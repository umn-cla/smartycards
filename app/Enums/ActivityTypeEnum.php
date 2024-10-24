<?php

namespace App\Enums;

enum ActivityTypeEnum: string
{
    case CREATE_CARD = 'create-card';
    case PRACTICE_CARD = 'practice-card';
    case PRACTICE_ALL_CARDS = 'practice-all-cards';
    case QUIZ = 'quiz';
    case MATCHING = 'matching';
}

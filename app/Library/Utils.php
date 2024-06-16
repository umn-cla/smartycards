<?php

namespace App\Library;

class Utils
{
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function getEmailUsername($email)
    {
        if (! self::isValidEmail($email)) {
            return null;
        }

        return explode('@', $email)[0];
    }
}

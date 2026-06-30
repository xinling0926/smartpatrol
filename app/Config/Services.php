<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use App\Libraries\Language;
use App\Libraries\Message;
use App\Libraries\Setting;
use App\Libraries\User;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * The Message library for handling system messages.
     *
     * @return Message
     */
    public static function message(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('message');
        }

        return new Message();
    }
    /**
     * The Language class with CI3 compatibility.
     *
     * @return Language
     */
    public static function language(?string $locale = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('language', $locale);
        }

        if ($locale === null) {
            $locale = static::request()->getLocale();
        }

        return new Language($locale);
    }

    /**
     * The User library for authentication and user management.
     *
     * @return User
     */
    public static function user(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('user');
        }

        return new User();
    }

    /**
     * The Setting library for system configuration.
     *
     * @return Setting
     */
    public static function setting(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('setting');
        }

        return new Setting();
    }
}

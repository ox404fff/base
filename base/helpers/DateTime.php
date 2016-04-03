<?php

namespace app\base\helpers;

/**
 * Functions for working with time
 *
 * Class DateTime
 * @package app\base\helpers
 */
class DateTime
{

    /**
     * Count seconds in period
     */
    const SECOND = 1;

    /**
     * seconds in a minute
     */
    const MINUTE = 60;

    /**
     * seconds in a hour
     */
    const HOUR = 3600;

    /**
     * seconds in a day
     */
    const DAY = 86400;

    /**
     * seconds in a week
     */
    const WEEK = 604800;

    /**
     * @var int|null the ability to install any current time
     */
    public static $time;

    /**
     * Get current time stamp
     *
     * @return int
     */
    public static function time()
    {
        if (!is_null(self::$time)) {
            return self::$time;
        }
        return time();
    }

} 
<?php

namespace Loouss\ObsClient\Internal\Common;

class SchemaFormatter
{
    protected static $utcTimeZone;

    public static function format($fmt, $value)
    {
        if ($fmt === 'date-time') {
            return self::formatDateTime($value);
        }

        if ($fmt === 'data-time-http') {
            return self::formatDateTimeHttp($value);
        }

        if ($fmt === 'data-time-middle') {
            return self::formatDateTimeMiddle($value);
        }

        if ($fmt === 'date') {
            return self::formatDate($value);
        }

        if ($fmt === 'timestamp') {
            return self::formatTimestamp($value);
        }

        if ($fmt === 'boolean-string') {
            return self::formatBooleanAsString($value);
        }

        return $value;
    }

    public static function formatDateTimeMiddle($dateTime)
    {
        if (is_string($dateTime)) {
            $dateTime = new \DateTime($dateTime);
        }

        if ($dateTime instanceof \DateTime) {
            return $dateTime->format('Y-m-d\T00:00:00\Z');
        }
        return null;
    }

    public static function formatDateTime($value)
    {
        return self::dateFormatter($value, 'Y-m-d\TH:i:s\Z');
    }

    public static function formatDateTimeHttp($value)
    {
        return self::dateFormatter($value, 'D, d M Y H:i:s \G\M\T');
    }

    public static function formatDate($value)
    {
        return self::dateFormatter($value, 'Y-m-d');
    }

    public static function formatTime($value)
    {
        return self::dateFormatter($value, 'H:i:s');
    }

    public static function formatBooleanAsString($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    }

    public static function formatTimestamp($value)
    {
        return (int) self::dateFormatter($value, 'U');
    }

    private static function dateFormatter($dt, $fmt)
    {
        if (is_numeric($dt)) {
            return gmdate($fmt, (int) $dt);
        }

        if (is_string($dt)) {
            $dt = new \DateTime($dt);
        }

        if ($dt instanceof \DateTime) {
            if (!self::$utcTimeZone) {
                self::$utcTimeZone = new \DateTimeZone('UTC');
            }

            return $dt->setTimezone(self::$utcTimeZone)->format($fmt);
        }

        return null;
    }
}

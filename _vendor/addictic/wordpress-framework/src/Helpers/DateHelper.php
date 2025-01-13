<?php

namespace Addictic\WordpressFramework\Helpers;

class DateHelper
{
    public static function parseFromFormat($formatFrom, $formatTo, $value)
    {
        return date_i18n($formatTo, \DateTime::createFromFormat($formatFrom, $value)->getTimestamp());
    }

    public static function parse($format, $tstamp)
    {
        return date_i18n($format, $tstamp);
    }

    public static function fromFormat($format, $value)
    {
        $parsed = \DateTime::createFromFormat($format, $value);
        return $parsed ? $parsed->getTimestamp() : null;
    }
}
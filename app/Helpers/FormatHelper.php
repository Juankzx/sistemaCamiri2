<?php

namespace App\Helpers;

use NumberFormatter;

class FormatHelper
{
    public static function formatCurrency($value, $currency = 'CLP', $locale = 'es_CL')
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($value, $currency);
    }
}

<?php

use Carbon\Carbon;

if (! function_exists('format_fecha')) {
    function format_fecha($fecha): string
    {
        if (blank($fecha)) {
            return '';
        }

        try {
            return Carbon::parse($fecha)->format('d/m/Y');
        } catch (\Throwable $e) {
            return (string) $fecha;
        }
    }
}

if (! function_exists('format_numero')) {
    function format_numero($n, $d=2){
        return number_format((float)$n, $d, '.', ','); // bonito
    }
}
        
if (! function_exists('format_numero_raw')) {
    function format_numero_raw($n, $d=2){
        return number_format((float)$n, $d, '.', ''); // en vez de (string)(int) round(...)
    }
}
        
if (! function_exists('format_view_usd')) {
    function format_view_usd($valor){
        return floor($valor * 2) / 2; //Se redondea HACIA ABAJO al múltiplo de 0.5 más cercano.
    }
}
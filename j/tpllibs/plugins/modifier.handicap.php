<?php
/**
 * Smarty plugin
 *
 * This plugin is only for Smarty2 BC
 * @package Smarty
 * @subpackage PluginsFunction
 */


/**
 * Smarty {math} function plugin
 *
 * Type:     modifier<br>
 * Name:     status<br>
 * Purpose:  handle math computations in template<br>
 *          (Smarty online manual)
 * @author   Justin Wilson <justin.wilson@foxmail.com>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_modifier_handicap($param)
{
    if($param === ""){return "-";}
    switch (number_format($param,2)) {
        case "0":
            return "平手";
        case "0.00":
            return "平手";
        case "-0.25":
            return "平手/半球";
        case "-0.50":
            return "半球";
        case "-0.75":
            return "半球/一球";
        case "-1.00":
            return "一球";
        case "-1.25":
            return "一球/球半";
        case "-1.50":
            return "球半";
        case "-1.75":
            return "球半/两球";
        case "-2.00":
            return "两球";
        case "-2.25":
            return "两球/两球半";
        case "-2.50":
            return "两球半";
        case "-2.75":
            return "两球半/三球";
        case "-3.00":
            return "三球";
        case "-3.25":
            return "三球/三球半";
        case "-3.50":
            return "三球半";
        case "-3.75":
            return "三球半/四球";
        case "-4.00":
            return "四球";
        case "-4.25":
            return "四球/四球半";
        case "-4.50":
            return "四球半";
        case "-4.75":
            return "四球半/五球";
        case "-5.00":
            return "五球";
        case "-5.25":
            return "五球/五球半";
        case "-5.50":
            return "五球半";
        case "-5.75":
            return "五球半/六球";
        case "-6.00":
            return "六球";
        case "-6.25":
            return "六球/六球半";
        case "-6.50":
            return "六球半";
        case "-6.75":
            return "六球半/七球";
        case "-7.00":
            return "七球";
        case "-7.25":
            return "七球/七球半";
        case "-7.50":
            return "七球半";
        case "-7.75":
            return "七球半/八球";
        case "-8.00":
            return "八球";
        case "-8.25":
            return "八球/八球半";
        case "-8.50":
            return "八球半";
        case "-8.75":
            return "八球半/九球";
        case "-9.00":
            return "九球";
        case "-9.25":
            return "九球/九球半";
        case "-9.50":
            return "九球半";
        case "-9.75":
            return "九球半/十球";
        case "-10.00":
            return "十球";
        case "0.25":
            return "受让平手/半球";
        case "0.50":
            return "受让半球";
        case "0.75":
            return "受让半球/一球";
        case "1.00":
            return "受让一球";
        case "1.25":
            return "受让一球/球半";
        case "1.50":
            return "受让球半";
        case "1.75":
            return "受让球半/两球";
        case "2.00":
            return "受让两球";
        case "2.25":
            return "受让两球/两球半";
        case "2.50":
            return "受让两球半";
        case "2.75":
            return "受让两球半/三球";
        case "3.00":
            return "受让三球";
        case "3.25":
            return "受三球/三球半";
        case "3.50":
            return "受三球半";
        case "3.75":
            return "受三球半/四球";
        case "4.00":
            return "受四球";
        case "4.25":
            return "受四球/四球半";
        case "4.50":
            return "受四球半";
        case "4.75":
            return "受四球半/五球";
        case "5.00":
            return "受五球";
        case "5.25":
            return "受五球/五球半";
        case "5.50":
            return "受五球半";
        case "5.75":
            return "受五球半/六球";
        case "6.00":
            return "受六球";
        case "6.25":
            return "受六球/六球半";
        case "6.50":
            return "受六球半";
        case "6.75":
            return "受六球半/七球";
        case "7.00":
            return "受七球";
        case "7.25":
            return "受七球/七球半";
        case "7.50":
            return "受七球半";
        case "7.75":
            return "受七球半/八球";
        case "8.00":
            return "受八球";
        case "8.25":
            return "受八球/八球半";
        case "8.50":
            return "受八球半";
        case "8.75":
            return "受八球半/九球";
        case "9.00":
            return "受九球";
        case "9.25":
            return "受九球/九球半";
        case "9.50":
            return "受九球半";
        case "9.75":
            return "受九球半/十球";
        case "10.00":
            return "受十球";
        default:
            return "-";
        }
}

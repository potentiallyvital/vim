<?php

/*
for ($i=0; $i<=100; $i++)
{
        echo "$i : \e[0;'.$i.'m text \e[0m\r\n";
}
*/

const DATE_FORMAT = 'M d Y';

const COLOR_WHITE = 0;
const COLOR_GREY = 2;
const COLOR_RED = 91;
const COLOR_GREEN = 92;

const COLOR_DIR = 94;
const COLOR_LINK = 96;
const COLOR_PHP = 35;
const COLOR_HTML = 32;

const COLOR_HTACCESS = 91;
const COLOR_CONF = 91;
const COLOR_SQL = 91;
const COLOR_TXT = 91;

const COLOR_OTHER = 0;

function pad($string, $digits, $color)
{
        while (strlen($string) < $digits)
        {
                $string = ' '.$string;
        }
        return color($string, $color);
}

function color($string, $color = null)
{
        if (defined($color))
        {
                $color = constant($color);
        }
        else
        {
                $color = 'COLOR_'.strtoupper($color);
                if (defined($color))
                {
                        $color = constant($color);
                }
                else
                {
                        $color = COLOR_OTHER;
                }
        }
        return "\e[0;{$color}m{$string}\e[0m";
}

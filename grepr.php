<?php

include 'bash.php';

$file = array_shift($argv);
$string = array_shift($argv);

if (empty($argv))
{
        $argv[] = '*';
}

$lines = [];
foreach ($argv as $input_file)
{
        $input_length = strlen($input_file);

        $results = trim(shell_exec('grep -r "'.$string.'" '.$input_file));
        if ($results)
        {
                $results = explode(PHP_EOL, $results);
                foreach ($results as $row)
                {
                        if ($input_file != '*' && substr($row, 0, $input_length) != $input_file)
                        {
                                $path = $input_file;
                                $contents = trim($row);
                        }
                        else
                        {
                                $parts = explode(':', $row);
                                $path = array_shift($parts);
                                $contents = trim(implode(':', $parts));
                        }

                        $contents = str_replace($string, color($string, 'RED'), $contents);

                        $path = explode('/', $path);
                        $file = array_pop($path);
                        $path = implode('/', $path);

                        $type = explode('.', $file);
                        $type = array_pop($type);

                        $lines[$path.$file][] = color("$path/", 'GREY').color($file, $type).'   '.$contents;
                }
        }
}

echo "\r\n";
if ($lines)
{
        ksort($lines);
        foreach ($lines as $path => $path_lines)
        {
                foreach ($path_lines as $path_line)
                {
                        echo " $path_line\r\n";
                }
        }
}
else
{
        echo ' '.color('no occurances of "'.$string.'" found', 'GREY')."\r\n";
}
echo "\r\n";

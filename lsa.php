<?php

include 'bash.php';

if (!defined('RECURSIVE'))
{
        define('RECURSIVE', false);
}
if (!defined('ORDER'))
{
        define('ORDER', 'name');
}

array_shift($argv);
if (empty($argv))
{
        $argv = ['.'];
}

run($argv);

function run($dirs)
{
        echo "\r\n";
        echo "    Owner    Perms    Modified      GB     MB     KB     Path\r\n";
        echo "-----------+-------+-------------+------+------+------+-------------------\r\n";
        foreach ($dirs as $dir)
        {
                lsa($dir);
        }
        echo "\r\n";
}

function lsa($dir)
{
        $base_dir = (substr($dir, 0, 1) == '/' ? '' : getcwd().'/');

        $dir = rtrim($dir, '/');

        $file_data = [];

        if (is_dir($dir))
        {
                $dir = realpath($dir).'/';
                $base_dir = '/';
                $files = scandir($dir);
        }
        elseif (is_file($dir))
        {
                $dir = rtrim($dir, '/');
                $file = basename($dir);
                $dir = rtrim($dir, $file);
                $files = [$file];
        }
        else
        {
                return;
        }
        foreach ($files as $file)
        {
                if ($file != '.' && $file != '..')
                {
                        $base_path = $base_dir.$dir;
                        $base_path = str_replace('/./', '/', $base_path);
                        $base_path = str_replace('//', '/', $base_path);
                        $path = $base_path.$file;

                        $is_dir = (is_dir($path));
                        $is_link = (is_link($path));

                        $type = explode('.', $file);
                        $type = ($is_dir ? 'DIR' : array_pop($type));

                        $modified = filemtime($path);

                        $owner = posix_getpwuid(fileowner($path))['name'];

                        $permissions = substr(sprintf('%o', fileperms($path)), -4);

                        if ($is_dir)
                        {
                                $size = trim(shell_exec('du -s -B1 '.$path));
                                $size = explode("\t", $size);
                                $size = array_shift($size);
                        }
                        else
                        {
                                $size = filesize($path);
                        }

                        $data = [];
                        $data['dir'] = $dir;
                        $data['path'] = $path;
                        $data['name'] = strtolower($file);
                        $data['size'] = $size;
                        $data['type'] = $type;
                        $data['modified'] = $modified;
                        $data['line'] = '';
                        $data['line'] .= pad($owner, 10, 'WHITE');
                        $data['line'] .= ' ';
                        $data['line'] .= pad($permissions, 6, 'RED');
                        $data['line'] .= '   ';
                        $data['line'] .= modified($modified, 'GREY');
                        $data['line'] .= ' ';
                        $data['line'] .= size($size, 'GREY');
                        $data['line'] .= ' ';
                        $data['line'] .= color($base_path, 'GREY');
                        $data['line'] .= color($file, ($is_link ? 'LINK' : $type));

                        if ($is_link)
                        {
                                $data['line'] .= color(' -> '.readlink($path), 'LINK');
                        }

                        if ($is_dir && ORDER == 'name')
                        {
                                $data[ORDER] = 'zzzzzz';
                        }

                        $file_data[$data[ORDER]][] = $data;
                }
        }

        if (defined('DESC'))
        {
                krsort($file_data);
        }
        else
        {
                ksort($file_data);
        }

        foreach ($file_data as $ordered => $files)
        {
                foreach ($files as $aFile)
                {
                        echo $aFile['line']."\r\n";
                        if (RECURSIVE && is_dir($aFile['path']))
                        {
                                lsa($aFile['path']);
                        }
                }
        }
}

function size($size, $color = 'GREY')
{
        $gb = 0;
        $mb = 0;
        $kb = 0;

        $gb_color = $color;
        $mb_color = $color;
        $kb_color = $color;

        while ($size >= (1024*1024*1024))
        {
                $gb++;
                $gb_color = 'GREEN';
                $size -= (1024*1024*1024);
        }
        while ($size >= (1024*1024))
        {
                $mb++;
                $mb_color = 'GREEN';
                $size -= (1024*1024);
        }
        while ($size >= (1024))
        {
                $kb++;
                $kb_color = 'GREEN';
                $size -= (1024);
        }

        return pad($gb, 5, $gb_color).'  '.pad($mb, 5, $mb_color).'  '.pad($kb, 5, $kb_color).'  ';
}

function modified($time, $color = 'WHITE')
{
        $time = date(DATE_FORMAT, $time);

        return color(" $time ", $color);
}


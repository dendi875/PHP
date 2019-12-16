<?php

class FileSystem
{
    const FILE_MODE = '0644';

    public static function createDir($dir)
    {
        mkdir($dir);
    }
}
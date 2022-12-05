<?php

class DB
{
    public static function get_connect()
    {
        $host = 'localhost';
        $user = 'root';
        $password = 'root';
        $dbname = 'kit';
        $connect = null;

        try {
            $connect = new PDO("mysql:host={$host};dbname={$dbname};charset=UTF8", $user, $password);
        } catch (\Throwable $th) {}

        return $connect;
    }
}

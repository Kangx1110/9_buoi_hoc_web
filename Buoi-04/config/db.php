<?php
// config/db.php

function getDB()
{
    $host = 'localhost';
    $port = '3306';
    $dbname = 'cong_thong_tin_sv';
    $username = 'root';
    $password = '';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    try {
        $db = new PDO($dsn, $username, $password);

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $db;

    } catch (PDOException $e) {
        die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
    }
}
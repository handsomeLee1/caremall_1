<?php
// 데이터베이스 연결 설정
$host = 'localhost';
$dbname = 'caremall';
$username = 'root';
$password = '2744dlqsleK!';

try {
    // PDO 연결 시도
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e) {
    error_log("데이터베이스 연결 실패: " . $e->getMessage());
    die("데이터베이스 연결에 실패했습니다.");
}
?> 
<?php
$servername = "localhost";
$username = "root";      // XAMPP의 기본 사용자명
$password = "";          // XAMPP의 기본 비밀번호
$dbname = "caremall_1";  // 실제 데이터베이스 이름으로 수정

// 데이터베이스 연결 생성
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

// 문자셋 설정
$conn->set_charset("utf8mb4");
?> 
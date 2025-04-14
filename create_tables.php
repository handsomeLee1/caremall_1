<?php
require_once 'includes/config.php';

try {
    // 카테고리 테이블 생성
    $sql = "CREATE TABLE IF NOT EXISTS 카테고리 (
        카테고리ID INT AUTO_INCREMENT PRIMARY KEY,
        카테고리명 VARCHAR(50) NOT NULL,
        생성일시 TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    echo "<p style='color: green;'>카테고리 테이블이 성공적으로 생성되었습니다.</p>";

    // 상품 테이블 생성
    $sql = "CREATE TABLE IF NOT EXISTS 상품 (
        상품ID INT AUTO_INCREMENT PRIMARY KEY,
        카테고리ID INT,
        세부분류ID INT,
        상품명 VARCHAR(100) NOT NULL,
        상품코드 VARCHAR(50) NOT NULL,
        공급가 INT NOT NULL,
        재고수량 INT DEFAULT 0,
        제품설명 TEXT,
        등록일 TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        수정일 TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (카테고리ID) REFERENCES 카테고리(카테고리ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    echo "<p style='color: green;'>상품 테이블이 성공적으로 생성되었습니다.</p>";

    // 판매상품 테이블 생성
    $sql = "CREATE TABLE IF NOT EXISTS 판매상품 (
        상품ID INT PRIMARY KEY,
        판매가격 INT NOT NULL,
        FOREIGN KEY (상품ID) REFERENCES 상품(상품ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    echo "<p style='color: green;'>판매상품 테이블이 성공적으로 생성되었습니다.</p>";

    // 대여상품 테이블 생성
    $sql = "CREATE TABLE IF NOT EXISTS 대여상품 (
        상품ID INT PRIMARY KEY,
        월대여료 INT NOT NULL,
        FOREIGN KEY (상품ID) REFERENCES 상품(상품ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    echo "<p style='color: green;'>대여상품 테이블이 성공적으로 생성되었습니다.</p>";

} catch(PDOException $e) {
    echo "<p style='color: red;'>테이블 생성 중 오류가 발생했습니다: " . $e->getMessage() . "</p>";
}

// 테이블 생성 후 test_connection.php로 리다이렉트
echo "<p>3초 후 연결 테스트 페이지로 이동합니다...</p>";
echo "<script>setTimeout(function() { window.location.href = 'test_connection.php'; }, 3000);</script>";
?> 
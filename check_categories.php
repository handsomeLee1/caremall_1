<?php
require_once 'includes/config.php';

try {
    // 카테고리 테이블의 모든 데이터 조회
    $stmt = $db->query("SELECT * FROM 카테고리");
    echo "<h2>카테고리 테이블 데이터</h2>";
    
    if ($stmt->rowCount() > 0) {
        echo "<pre>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "카테고리 테이블에 데이터가 없습니다.";
    }
    
    // 테이블 구조도 확인
    echo "<h2>카테고리 테이블 구조</h2>";
    $stmt = $db->query("SHOW CREATE TABLE 카테고리");
    $tableInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($tableInfo);
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "오류 발생: " . $e->getMessage();
}
?> 
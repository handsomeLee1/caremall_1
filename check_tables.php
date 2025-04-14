<?php
require_once 'includes/config.php';

try {
    // 모든 테이블 목록 조회
    $stmt = $db->query("SHOW TABLES");
    echo "<h2>데이터베이스 테이블 목록</h2>";
    
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tableName = $row[0];
        echo "<h3>테이블: {$tableName}</h3>";
        
        // 테이블의 데이터 확인
        $data = $db->query("SELECT * FROM {$tableName} LIMIT 5");
        echo "<p>데이터 샘플:</p>";
        echo "<pre>";
        while ($record = $data->fetch(PDO::FETCH_ASSOC)) {
            print_r($record);
        }
        echo "</pre>";
        
        // 테이블 구조 확인
        $structure = $db->query("DESCRIBE {$tableName}");
        echo "<p>테이블 구조:</p>";
        echo "<pre>";
        while ($col = $structure->fetch(PDO::FETCH_ASSOC)) {
            print_r($col);
        }
        echo "</pre>";
        echo "<hr>";
    }
} catch(PDOException $e) {
    echo "오류 발생: " . $e->getMessage();
}
?> 
<?php
require_once 'includes/config.php';

try {
    // PDO 연결 테스트
    $test_query = $db->query('SELECT 1');
    echo "<h2 style='color: green;'>데이터베이스 연결 성공!</h2>";
    
    // 테이블 존재 여부 확인
    $tables = array('카테고리', '상품', '판매상품', '대여상품');
    echo "<h3>테이블 상태 확인:</h3>";
    echo "<ul>";
    
    foreach ($tables as $table) {
        try {
            $result = $db->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                echo "<li style='color: green;'>$table 테이블이 존재합니다.</li>";
                
                // 테이블 구조 출력
                $structure = $db->query("DESCRIBE $table");
                echo "<ul>";
                while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li>{$row['Field']} - {$row['Type']}</li>";
                }
                echo "</ul>";
            } else {
                echo "<li style='color: red;'>$table 테이블이 없습니다.</li>";
            }
        } catch (PDOException $e) {
            echo "<li style='color: red;'>$table 테이블 확인 중 오류: " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<h2 style='color: red;'>데이터베이스 연결 실패</h2>";
    echo "<p>오류 메시지: " . $e->getMessage() . "</p>";
}
?> 
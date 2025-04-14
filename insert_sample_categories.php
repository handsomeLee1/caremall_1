<?php
require_once 'includes/config.php';

$sample_categories = [
    '의료기기',
    '복지용구',
    '위생용품',
    '재활용품',
    '영양식품'
];

try {
    foreach ($sample_categories as $category) {
        $stmt = $db->prepare("INSERT INTO 카테고리 (카테고리명) VALUES (:name)");
        if ($stmt->execute(['name' => $category])) {
            echo "<p style='color: green;'>'{$category}' 카테고리가 추가되었습니다.</p>";
        } else {
            echo "<p style='color: red;'>'{$category}' 카테고리 추가 실패.</p>";
        }
    }
    
    echo "<p>3초 후 카테고리 관리 페이지로 이동합니다...</p>";
    echo "<script>setTimeout(function() { window.location.href = 'pages/category/list.php'; }, 3000);</script>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>오류 발생: " . $e->getMessage() . "</p>";
}
?> 
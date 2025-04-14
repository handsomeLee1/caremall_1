<?php
require_once 'config.php';
require_once 'Customer.php';

try {
    $customer = new Customer($db);
    
    // 테스트 데이터 준비
    $testData = [
        'name' => '홍길동',
        'birthdate' => '1950-01-01',
        'gender' => '남',
        'phone' => '010-1234-5678',
        'address' => '서울시 강남구 테스트동 123',
        'careNumber' => '12345678',
        'recipientType' => '일반',
        'chargeRate' => '15%',
        'grade' => '3등급',
        'startDate' => date('Y-m-d'),
        'endDate' => date('Y-m-d', strtotime('+1 year')),
        'guardianName' => '홍판서',
        'guardianRelation' => '자녀',
        'guardianPhone' => '010-9876-5432',
        'notes' => '테스트 데이터입니다.'
    ];
    
    // 데이터 입력 시도
    if ($customer->addCustomer($testData)) {
        echo "<h2>테스트 데이터 입력 성공!</h2>";
        echo "<p>다음 데이터가 입력되었습니다:</p>";
        echo "<pre>";
        print_r($testData);
        echo "</pre>";
        
        // 입력된 데이터 확인
        echo "<h3>데이터베이스에서 조회된 고객 목록:</h3>";
        $customers = $customer->getCustomers();
        echo "<pre>";
        print_r($customers);
        echo "</pre>";
    } else {
        echo "<h2>테스트 데이터 입력 실패</h2>";
    }
    
} catch(Exception $e) {
    echo "<h2>에러 발생</h2>";
    echo "<p>에러 메시지: " . $e->getMessage() . "</p>";
}

// 테스트 페이지로 돌아가는 링크
echo "<p><a href='test_connection.php'>데이터베이스 연결 테스트로 돌아가기</a></p>";
echo "<p><a href='customer_list.php'>고객 목록 페이지로 가기</a></p>";
?> 
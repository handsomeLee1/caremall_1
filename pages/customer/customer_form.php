<?php
require_once '../../includes/config.php';
require_once '../../classes/Customer.php';

$customer = new Customer($db);
$customerData = null;
$isEdit = false;

if (isset($_GET['id'])) {
    $isEdit = true;
    $customerData = $customer->getCustomer($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'birthdate' => $_POST['birthdate'],
        'gender' => $_POST['gender'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'careNumber' => $_POST['careNumber'],
        'recipientType' => $_POST['recipientType'],
        'chargeRate' => $_POST['chargeRate'],
        'grade' => $_POST['grade'],
        'startDate' => $_POST['startDate'],
        'endDate' => $_POST['endDate'],
        'guardianName' => $_POST['guardianName'],
        'guardianRelation' => $_POST['guardianRelation'],
        'guardianPhone' => $_POST['guardianPhone'],
        'notes' => $_POST['notes']
    ];

    if ($isEdit) {
        if ($customer->updateCustomer($_GET['id'], $data)) {
            header('Location: customer_list.php');
            exit;
        }
    } else {
        if ($customer->addCustomer($data)) {
            header('Location: customer_list.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? '고객 정보 수정' : '신규 고객 등록'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2><?php echo $isEdit ? '고객 정보 수정' : '신규 고객 등록'; ?></h2>
        
        <form method="post" class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <h4>기본 정보</h4>
                    <div class="mb-3">
                        <label for="name" class="form-label">이름</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($customerData['이름'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">생년월일</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate"
                               value="<?php echo $customerData['생년월일'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gender" class="form-label">성별</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">선택하세요</option>
                            <option value="남" <?php echo ($customerData['성별'] ?? '') === '남' ? 'selected' : ''; ?>>남</option>
                            <option value="여" <?php echo ($customerData['성별'] ?? '') === '여' ? 'selected' : ''; ?>>여</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">전화번호</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($customerData['전화번호'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">주소</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php 
                            echo htmlspecialchars($customerData['주소'] ?? ''); 
                        ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h4>요양 정보</h4>
                    <div class="mb-3">
                        <label for="careNumber" class="form-label">장기요양인정번호</label>
                        <input type="text" class="form-control" id="careNumber" name="careNumber"
                               value="<?php echo htmlspecialchars($customerData['장기요양인정번호'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="recipientType" class="form-label">수급자구분</label>
                        <select class="form-select" id="recipientType" name="recipientType">
                            <option value="">선택하세요</option>
                            <option value="일반" <?php echo ($customerData['수급자구분'] ?? '') === '일반' ? 'selected' : ''; ?>>일반</option>
                            <option value="의료" <?php echo ($customerData['수급자구분'] ?? '') === '의료' ? 'selected' : ''; ?>>의료</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="chargeRate" class="form-label">부담율</label>
                        <select class="form-select" id="chargeRate" name="chargeRate">
                            <option value="">선택하세요</option>
                            <option value="0%" <?php echo ($customerData['부담율'] ?? '') === '0%' ? 'selected' : ''; ?>>0%</option>
                            <option value="6%" <?php echo ($customerData['부담율'] ?? '') === '6%' ? 'selected' : ''; ?>>6%</option>
                            <option value="9%" <?php echo ($customerData['부담율'] ?? '') === '9%' ? 'selected' : ''; ?>>9%</option>
                            <option value="15%" <?php echo ($customerData['부담율'] ?? '') === '15%' ? 'selected' : ''; ?>>15%</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="grade" class="form-label">등급</label>
                        <select class="form-select" id="grade" name="grade">
                            <option value="">선택하세요</option>
                            <option value="1등급" <?php echo ($customerData['등급'] ?? '') === '1등급' ? 'selected' : ''; ?>>1등급</option>
                            <option value="2등급" <?php echo ($customerData['등급'] ?? '') === '2등급' ? 'selected' : ''; ?>>2등급</option>
                            <option value="3등급" <?php echo ($customerData['등급'] ?? '') === '3등급' ? 'selected' : ''; ?>>3등급</option>
                            <option value="4등급" <?php echo ($customerData['등급'] ?? '') === '4등급' ? 'selected' : ''; ?>>4등급</option>
                            <option value="5등급" <?php echo ($customerData['등급'] ?? '') === '5등급' ? 'selected' : ''; ?>>5등급</option>
                            <option value="인지지원등급" <?php echo ($customerData['등급'] ?? '') === '인지지원등급' ? 'selected' : ''; ?>>인지지원등급</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="startDate" class="form-label">유효기간 시작일</label>
                        <input type="date" class="form-control" id="startDate" name="startDate"
                               value="<?php echo $customerData['유효기간시작일'] ?? ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="endDate" class="form-label">유효기간 종료일</label>
                        <input type="date" class="form-control" id="endDate" name="endDate"
                               value="<?php echo $customerData['유효기간종료일'] ?? ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="guardianName" class="form-label">보호자 이름</label>
                        <input type="text" class="form-control" id="guardianName" name="guardianName"
                               value="<?php echo htmlspecialchars($customerData['보호자이름'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="guardianRelation" class="form-label">보호자 관계</label>
                        <select class="form-select" id="guardianRelation" name="guardianRelation">
                            <option value="">선택하세요</option>
                            <option value="배우자" <?php echo ($customerData['보호자관계'] ?? '') === '배우자' ? 'selected' : ''; ?>>배우자</option>
                            <option value="자녀" <?php echo ($customerData['보호자관계'] ?? '') === '자녀' ? 'selected' : ''; ?>>자녀</option>
                            <option value="형제" <?php echo ($customerData['보호자관계'] ?? '') === '형제' ? 'selected' : ''; ?>>형제</option>
                            <option value="기타" <?php echo ($customerData['보호자관계'] ?? '') === '기타' ? 'selected' : ''; ?>>기타</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="guardianPhone" class="form-label">보호자 전화번호</label>
                        <input type="tel" class="form-control" id="guardianPhone" name="guardianPhone"
                               value="<?php echo htmlspecialchars($customerData['보호자전화번호'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">비고</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php 
                            echo htmlspecialchars($customerData['비고'] ?? ''); 
                        ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">저장</button>
                <a href="customer_list.php" class="btn btn-secondary">취소</a>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
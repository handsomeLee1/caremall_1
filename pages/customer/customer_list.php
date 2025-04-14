<?php
require_once '../../includes/config.php';
require_once '../../classes/Customer.php';

$customer = new Customer($db);
$customers = $customer->getCustomers();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>고객 관리</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>고객 목록</h2>
        <div class="mb-3">
            <a href="customer_form.php" class="btn btn-primary">신규 고객 등록</a>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>고객ID</th>
                    <th>이름</th>
                    <th>생년월일</th>
                    <th>전화번호</th>
                    <th>등급</th>
                    <th>수급자구분</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['고객ID']); ?></td>
                    <td><?php echo htmlspecialchars($customer['이름']); ?></td>
                    <td><?php echo htmlspecialchars($customer['생년월일']); ?></td>
                    <td><?php echo htmlspecialchars($customer['전화번호']); ?></td>
                    <td><?php echo htmlspecialchars($customer['등급'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($customer['수급자구분'] ?? ''); ?></td>
                    <td>
                        <a href="customer_form.php?id=<?php echo $customer['고객ID']; ?>" 
                           class="btn btn-sm btn-info">수정</a>
                        <a href="customer_delete.php?id=<?php echo $customer['고객ID']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('정말 삭제하시겠습니까?');">삭제</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
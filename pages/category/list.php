<?php
require_once '../../includes/config.php';
require_once '../../classes/Category.php';

$category = new Category($db);
$categories = $category->getCategories();

// 성공/실패 메시지 처리
$message = '';
$message_type = '';

if (isset($_GET['success'])) {
    $message_type = 'success';
    switch ($_GET['success']) {
        case 'add':
            $message = '카테고리가 성공적으로 추가되었습니다.';
            break;
        case 'update':
            $message = '카테고리가 성공적으로 수정되었습니다.';
            break;
        case 'delete':
            $message = '카테고리가 성공적으로 삭제되었습니다.';
            break;
    }
} elseif (isset($_GET['error'])) {
    $message_type = 'danger';
    switch ($_GET['error']) {
        case 'delete':
            $message = '이 카테고리에 연결된 상품이 있어 삭제할 수 없습니다.';
            break;
        default:
            $message = '작업 중 오류가 발생했습니다.';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>카테고리 관리</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>카테고리 관리</h2>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- 카테고리 추가 폼 -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">새 카테고리 추가</h5>
                <form action="process.php" method="post" class="row g-3">
                    <input type="hidden" name="action" value="add">
                    <div class="col-auto">
                        <input type="text" class="form-control" name="category_name" placeholder="카테고리명" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">추가</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 카테고리 목록 -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">카테고리 목록</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>카테고리명</th>
                            <th>생성일시</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr id="row_<?php echo $cat['카테고리ID']; ?>">
                            <td><?php echo htmlspecialchars($cat['카테고리ID']); ?></td>
                            <td>
                                <span class="category-name"><?php echo htmlspecialchars($cat['카테고리명']); ?></span>
                                <form class="edit-form d-none" action="process.php" method="post">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="category_id" value="<?php echo $cat['카테고리ID']; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" name="category_name" 
                                               value="<?php echo htmlspecialchars($cat['카테고리명']); ?>">
                                        <button type="submit" class="btn btn-success btn-sm">저장</button>
                                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">취소</button>
                                    </div>
                                </form>
                            </td>
                            <td><?php echo $cat['생성일시']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-info edit-category">수정</button>
                                <a href="process.php?action=delete&id=<?php echo $cat['카테고리ID']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('정말 삭제하시겠습니까?');">삭제</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 수정 버튼 클릭
        document.querySelectorAll('.edit-category').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelector('.category-name').classList.add('d-none');
                row.querySelector('.edit-form').classList.remove('d-none');
                this.classList.add('d-none');
            });
        });

        // 취소 버튼 클릭
        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelector('.category-name').classList.remove('d-none');
                row.querySelector('.edit-form').classList.add('d-none');
                row.querySelector('.edit-category').classList.remove('d-none');
            });
        });
    });
    </script>
</body>
</html> 
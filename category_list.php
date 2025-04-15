<?php
require_once 'db_connect.php';

// 카테고리 추가 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
    
    if ($parent_id) {
        // 하위 카테고리 중복 체크
        $check_sql = "SELECT COUNT(*) as cnt FROM subcategories WHERE 카테고리ID = ? AND 세부분류명 = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $parent_id, $category_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['cnt'] > 0) {
            $error = "이미 존재하는 하위 카테고리입니다.";
        } else {
            $sql = "INSERT INTO subcategories (세부분류명, 카테고리ID) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $category_name, $parent_id);
            
            if ($stmt->execute()) {
                header("Location: category_list.php?success=1");
                exit();
            } else {
                $error = "카테고리 추가 실패: " . $conn->error;
            }
        }
    } else {
        // 상위 카테고리 중복 체크
        $check_sql = "SELECT COUNT(*) as cnt FROM categories WHERE 카테고리명 = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $category_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['cnt'] > 0) {
            $error = "이미 존재하는 카테고리입니다.";
        } else {
            $sql = "INSERT INTO categories (카테고리명) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category_name);
            
            if ($stmt->execute()) {
                header("Location: category_list.php?success=1");
                exit();
            } else {
                $error = "카테고리 추가 실패: " . $conn->error;
            }
        }
    }
}

// 카테고리 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $is_subcategory = isset($_POST['is_subcategory']) ? $_POST['is_subcategory'] : false;
    
    if ($is_subcategory) {
        $sql = "UPDATE subcategories SET 세부분류명 = ? WHERE 세부분류ID = ?";
    } else {
        $sql = "UPDATE categories SET 카테고리명 = ? WHERE 카테고리ID = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $category_name, $category_id);
    
    if ($stmt->execute()) {
        header("Location: category_list.php?success=3");
        exit();
    } else {
        $error = "카테고리 수정 실패: " . $conn->error;
    }
}

// 카테고리 삭제 처리
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $is_subcategory = isset($_GET['is_sub']) ? true : false;
    
    if ($is_subcategory) {
        $sql = "DELETE FROM subcategories WHERE 세부분류ID = ?";
    } else {
        // 상위 카테고리 삭제 시 하위 카테고리도 함께 삭제
        $sql = "DELETE FROM categories WHERE 카테고리ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        
        $sql = "DELETE FROM subcategories WHERE 카테고리ID = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    
    if ($stmt->execute()) {
        header("Location: category_list.php?success=2");
        exit();
    } else {
        $error = "카테고리 삭제 실패: " . $conn->error;
    }
}

// 수정할 카테고리 정보 가져오기
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $is_subcategory = isset($_GET['is_sub']) ? true : false;
    
    if ($is_subcategory) {
        $sql = "SELECT 세부분류ID as id, 세부분류명 as name, 카테고리ID as parent_id, 1 as is_sub FROM subcategories WHERE 세부분류ID = ?";
    } else {
        $sql = "SELECT 카테고리ID as id, 카테고리명 as name, 0 as is_sub FROM categories WHERE 카테고리ID = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_category = $stmt->get_result()->fetch_assoc();
}

// 모든 카테고리 목록 조회
$sql = "SELECT 카테고리ID, 카테고리명, NULL as parent_id, 0 as is_sub FROM categories 
        UNION ALL 
        SELECT 세부분류ID, 세부분류명, 카테고리ID, 1 as is_sub FROM subcategories 
        ORDER BY parent_id ASC, is_sub ASC, 카테고리ID ASC";
$result = $conn->query($sql);

// 상위 카테고리 목록 조회 (추가 폼용)
$parent_categories = $conn->query("SELECT 카테고리ID, 카테고리명 FROM categories ORDER BY 카테고리ID ASC");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>카테고리 관리</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .add-form, .edit-form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .add-form input[type="text"], .edit-form input[type="text"] {
            padding: 5px;
            margin-right: 10px;
            width: 200px;
        }
        .add-form select {
            padding: 5px;
            margin-right: 10px;
            width: 200px;
        }
        .add-form button, .edit-form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }
        .delete-btn {
            color: red;
            text-decoration: none;
            margin-left: 10px;
        }
        .edit-btn {
            color: #2196F3;
            text-decoration: none;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .subcategory {
            padding-left: 30px;
        }
        .subcategory::before {
            content: "└";
            margin-right: 5px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>카테고리 관리</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success">
                <?php if ($_GET['success'] == 1): ?>
                    카테고리가 성공적으로 추가되었습니다.
                <?php elseif ($_GET['success'] == 2): ?>
                    카테고리가 성공적으로 삭제되었습니다.
                <?php elseif ($_GET['success'] == 3): ?>
                    카테고리가 성공적으로 수정되었습니다.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($edit_category): ?>
            <div class="edit-form">
                <h3>카테고리 수정</h3>
                <form method="POST">
                    <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                    <input type="hidden" name="is_subcategory" value="<?php echo $edit_category['is_sub']; ?>">
                    <input type="text" name="category_name" value="<?php echo htmlspecialchars($edit_category['name']); ?>" required>
                    <button type="submit" name="edit_category">수정 완료</button>
                    <a href="category_list.php" style="margin-left: 10px; color: #666; text-decoration: none;">취소</a>
                </form>
            </div>
        <?php else: ?>
            <div class="add-form">
                <form method="POST">
                    <select name="parent_id">
                        <option value="">상위 카테고리 선택 (선택하지 않으면 최상위 카테고리)</option>
                        <?php while($category = $parent_categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['카테고리ID']; ?>">
                                <?php echo htmlspecialchars($category['카테고리명']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="category_name" placeholder="카테고리명" required>
                    <button type="submit" name="add_category">카테고리 추가</button>
                </form>
            </div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>카테고리명</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php 
                    $current_parent = null;
                    while($row = $result->fetch_assoc()): 
                    ?>
                        <tr class="<?php echo $row['parent_id'] ? 'subcategory' : ''; ?>">
                            <td><?php echo $row['카테고리ID']; ?></td>
                            <td><?php echo htmlspecialchars($row['카테고리명']); ?></td>
                            <td class="action-buttons">
                                <a href="category_list.php?edit=<?php echo $row['카테고리ID']; ?><?php echo $row['is_sub'] ? '&is_sub=1' : ''; ?>" 
                                   class="edit-btn">
                                    수정
                                </a>
                                <a href="category_list.php?delete=<?php echo $row['카테고리ID']; ?><?php echo $row['is_sub'] ? '&is_sub=1' : ''; ?>" 
                                   class="delete-btn"
                                   onclick="return confirm('정말로 이 카테고리를 삭제하시겠습니까?<?php echo !$row['is_sub'] ? '\n\n주의: 하위 카테고리도 모두 삭제됩니다!' : ''; ?>')">
                                    삭제
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">등록된 카테고리가 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 
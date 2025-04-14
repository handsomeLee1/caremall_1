<?php
require_once '../../includes/config.php';
require_once '../../classes/Category.php';

$category = new Category($db);

// POST 요청 처리 (추가/수정)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' && !empty($_POST['category_name'])) {
        if ($category->addCategory($_POST['category_name'])) {
            header('Location: list.php?success=add');
            exit;
        }
    }
    elseif ($action === 'update' && !empty($_POST['category_id']) && !empty($_POST['category_name'])) {
        if ($category->updateCategory($_POST['category_id'], $_POST['category_name'])) {
            header('Location: list.php?success=update');
            exit;
        }
    }
}
// GET 요청 처리 (삭제)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'delete' && !empty($_GET['id'])) {
        if ($category->deleteCategory($_GET['id'])) {
            header('Location: list.php?success=delete');
            exit;
        } else {
            header('Location: list.php?error=delete');
            exit;
        }
    }
}

// 오류 발생 시
header('Location: list.php?error=1');
exit; 
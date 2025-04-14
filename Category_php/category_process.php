<?php
require_once 'config.php';
require_once 'Category.php';

$category = new Category($db);
$action = $_POST['action'] ?? '';

switch($action) {
    case 'add':
        if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
            if ($category->addCategory($_POST['category_name'])) {
                header('Location: category_list.php?success=1');
            } else {
                header('Location: category_list.php?error=1');
            }
        }
        break;

    case 'update':
        if (isset($_POST['category_id'], $_POST['category_name']) && 
            !empty($_POST['category_name'])) {
            if ($category->updateCategory($_POST['category_id'], $_POST['category_name'])) {
                header('Location: category_list.php?success=2');
            } else {
                header('Location: category_list.php?error=2');
            }
        }
        break;

    case 'delete':
        if (isset($_POST['category_id'])) {
            if ($category->deleteCategory($_POST['category_id'])) {
                header('Location: category_list.php?success=3');
            } else {
                header('Location: category_list.php?error=3');
            }
        }
        break;

    default:
        header('Location: category_list.php');
}

exit;
?> 
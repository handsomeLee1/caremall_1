<?php
require_once '../../includes/config.php';
require_once '../../classes/Customer.php';

if (isset($_GET['id'])) {
    $customer = new Customer($db);
    if ($customer->deleteCustomer($_GET['id'])) {
        header('Location: customer_list.php');
        exit;
    } else {
        echo "삭제 중 오류가 발생했습니다.";
    }
} else {
    header('Location: customer_list.php');
    exit;
}
?> 
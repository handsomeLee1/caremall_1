<?php
require_once __DIR__ . '/../includes/config.php';

class Category {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 카테고리 목록 조회
    public function getCategories() {
        try {
            $stmt = $this->db->query("
                SELECT 카테고리ID, 카테고리명 
                FROM categories 
                ORDER BY 카테고리ID ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("카테고리 조회 중 오류: " . $e->getMessage());
            return [];
        }
    }

    // 단일 카테고리 조회
    public function getCategory($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 카테고리ID, 카테고리명 
                FROM categories 
                WHERE 카테고리ID = :id
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // 카테고리 추가
    public function addCategory($name) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO categories (카테고리명) 
                VALUES (:name)
            ");
            return $stmt->execute(['name' => $name]);
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // 카테고리 수정
    public function updateCategory($id, $name) {
        try {
            $stmt = $this->db->prepare("
                UPDATE categories 
                SET 카테고리명 = :name 
                WHERE 카테고리ID = :id
            ");
            return $stmt->execute([
                'id' => $id,
                'name' => $name
            ]);
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // 카테고리 삭제
    public function deleteCategory($id) {
        try {
            // 먼저 이 카테고리를 참조하는 상품이 있는지 확인
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM products 
                WHERE 카테고리ID = :id
            ");
            $stmt->execute(['id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                return false; // 참조하는 상품이 있으면 삭제 불가
            }

            $stmt = $this->db->prepare("
                DELETE FROM categories 
                WHERE 카테고리ID = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
?> 
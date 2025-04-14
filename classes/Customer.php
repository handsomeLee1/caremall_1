<?php
require_once '../includes/config.php';

class Customer {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 고객 추가
    public function addCustomer($data) {
        try {
            // customers 테이블에 추가
            $stmt = $this->db->prepare("INSERT INTO customers (이름, 생년월일, 성별, 전화번호, 주소) 
                VALUES (:name, :birthdate, :gender, :phone, :address)");
            
            $stmt->execute([
                ':name' => $data['name'],
                ':birthdate' => $data['birthdate'],
                ':gender' => $data['gender'],
                ':phone' => $data['phone'],
                ':address' => $data['address']
            ]);
            
            $customerId = $this->db->lastInsertId();
            
            // care_customers 테이블에 추가
            $stmt = $this->db->prepare("INSERT INTO care_customers (고객ID, 장기요양인정번호, 수급자구분, 
                부담율, 등급, 유효기간시작일, 유효기간종료일, 보호자이름, 보호자관계, 보호자전화번호, 비고) 
                VALUES (:customerId, :careNumber, :recipientType, :chargeRate, :grade, 
                :startDate, :endDate, :guardianName, :guardianRelation, :guardianPhone, :notes)");
            
            $stmt->execute([
                ':customerId' => $customerId,
                ':careNumber' => $data['careNumber'],
                ':recipientType' => $data['recipientType'],
                ':chargeRate' => $data['chargeRate'],
                ':grade' => $data['grade'],
                ':startDate' => $data['startDate'],
                ':endDate' => $data['endDate'],
                ':guardianName' => $data['guardianName'],
                ':guardianRelation' => $data['guardianRelation'],
                ':guardianPhone' => $data['guardianPhone'],
                ':notes' => $data['notes']
            ]);
            
            return true;
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // 고객 정보 수정
    public function updateCustomer($customerId, $data) {
        try {
            $this->db->beginTransaction();
            
            // customers 테이블 수정
            $stmt = $this->db->prepare("UPDATE customers SET 
                이름 = :name,
                생년월일 = :birthdate,
                성별 = :gender,
                전화번호 = :phone,
                주소 = :address
                WHERE 고객ID = :id");
            
            $stmt->execute([
                ':id' => $customerId,
                ':name' => $data['name'],
                ':birthdate' => $data['birthdate'],
                ':gender' => $data['gender'],
                ':phone' => $data['phone'],
                ':address' => $data['address']
            ]);
            
            // care_customers 테이블 수정
            $stmt = $this->db->prepare("UPDATE care_customers SET 
                장기요양인정번호 = :careNumber,
                수급자구분 = :recipientType,
                부담율 = :chargeRate,
                등급 = :grade,
                유효기간시작일 = :startDate,
                유효기간종료일 = :endDate,
                보호자이름 = :guardianName,
                보호자관계 = :guardianRelation,
                보호자전화번호 = :guardianPhone,
                비고 = :notes
                WHERE 고객ID = :id");
            
            $stmt->execute([
                ':id' => $customerId,
                ':careNumber' => $data['careNumber'],
                ':recipientType' => $data['recipientType'],
                ':chargeRate' => $data['chargeRate'],
                ':grade' => $data['grade'],
                ':startDate' => $data['startDate'],
                ':endDate' => $data['endDate'],
                ':guardianName' => $data['guardianName'],
                ':guardianRelation' => $data['guardianRelation'],
                ':guardianPhone' => $data['guardianPhone'],
                ':notes' => $data['notes']
            ]);
            
            $this->db->commit();
            return true;
        } catch(PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // 고객 삭제
    public function deleteCustomer($customerId) {
        try {
            $this->db->beginTransaction();
            
            // care_customers 테이블에서 먼저 삭제
            $stmt = $this->db->prepare("DELETE FROM care_customers WHERE 고객ID = :id");
            $stmt->execute([':id' => $customerId]);
            
            // customers 테이블에서 삭제
            $stmt = $this->db->prepare("DELETE FROM customers WHERE 고객ID = :id");
            $stmt->execute([':id' => $customerId]);
            
            $this->db->commit();
            return true;
        } catch(PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // 고객 목록 조회
    public function getCustomers() {
        try {
            $stmt = $this->db->query("
                SELECT c.*, cc.*
                FROM customers c
                LEFT JOIN care_customers cc ON c.고객ID = cc.고객ID
                ORDER BY c.고객ID DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // 단일 고객 조회
    public function getCustomer($customerId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, cc.*
                FROM customers c
                LEFT JOIN care_customers cc ON c.고객ID = cc.고객ID
                WHERE c.고객ID = :id
            ");
            $stmt->execute([':id' => $customerId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
?> 
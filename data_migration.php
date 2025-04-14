<?php
require_once 'config.php';

// Spreadsheet library
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

class DataMigration {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function migrateCustomers($spreadsheetPath, $sheetName) {
        try {
            $spreadsheet = IOFactory::load($spreadsheetPath);
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            
            $stmt = $this->db->prepare("INSERT INTO customers 
                (name, phone, email, address, registration_date) 
                VALUES (?, ?, ?, ?, ?)");
                
            foreach ($worksheet->getRowIterator(2) as $row) { // Skip header row
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                
                $stmt->execute([
                    $rowData[0], // name
                    $rowData[1], // phone
                    $rowData[2], // email
                    $rowData[3], // address
                    date('Y-m-d H:i:s') // registration_date
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Customer migration error: " . $e->getMessage());
            return false;
        }
    }
    
    public function migrateSales($spreadsheetPath, $sheetName) {
        try {
            $spreadsheet = IOFactory::load($spreadsheetPath);
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            
            $stmt = $this->db->prepare("INSERT INTO general_sales 
                (customer_id, sale_date, total_amount, payment_method, status) 
                VALUES (?, ?, ?, ?, ?)");
                
            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                
                // Find customer_id by name or other identifier
                $customerStmt = $this->db->prepare("SELECT id FROM customers WHERE name = ?");
                $customerStmt->execute([$rowData[0]]);
                $customerId = $customerStmt->fetchColumn();
                
                if ($customerId) {
                    $stmt->execute([
                        $customerId,
                        $rowData[1], // sale_date
                        $rowData[2], // total_amount
                        $rowData[3], // payment_method
                        'completed'  // status
                    ]);
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Sales migration error: " . $e->getMessage());
            return false;
        }
    }
}

// Usage example:
/*
$migration = new DataMigration($db);
$migration->migrateCustomers('customer_data.xlsx', 'Customers');
$migration->migrateSales('sales_data.xlsx', 'Sales');
*/
?> 
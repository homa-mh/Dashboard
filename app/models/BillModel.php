<?php
require_once 'app/Dbh.php';

class BillModel
{
    private $pdo;
    public function __construct(){
        $db = new Dbh();
        $this->pdo = $db->connect();
    }
    
    public function check($data): ?int {
        $user_id = $_SESSION['user_info']['id'];
        $title = $data['title'];

        $sql = "SELECT id FROM bill WHERE user_id = ? AND title = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $title]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            return $this->updateBill($data, $user_id, $existing['id']);
        } else {
            return $this->create($data);
        }
    }
    
    public function updateBill($data, $user_id, $bill_id): ?int {
        $sql = "UPDATE bill SET 
                    shenase = ?, 
                    bill_id = ?, 
                    trace_id = ?, 
                    payment_id = ?, 
                    full_name = ?, 
                    address = ?, 
                    amount = ?, 
                    payment_date = ?, 
                    previous_date = ?, 
                    bill_current_date = ?, 
                    bill_pdf_url = ?, 
                    inquired_date = ?
                WHERE id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $data['shenase'],
            $data['bill_id'],
            $data['trace_id'],
            $data['payment_id'],
            $data['full_name'],
            $data['address'],
            $data['amount'],
            $data['payment_date'],
            $data['previous_date'],
            $data['bill_current_date'],
            $data['bill_pdf_url'],
            date('Y-m-d'),
            $bill_id,
            $user_id
        ]);

        return $success ? $bill_id : null;
    }

    public function create($data): ?int {
        $sql = "INSERT INTO bill (user_id, title, shenase, bill_id, trace_id, payment_id, full_name, address, amount, payment_date, previous_date, bill_current_date, bill_pdf_url, inquired_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $_SESSION['user_info']['id'],
            $data['title'],
            $data['shenase'],
            $data['bill_id'],
            $data['trace_id'],
            $data['payment_id'],
            $data['full_name'],
            $data['address'],
            $data['amount'],
            $data['payment_date'],
            $data['previous_date'],
            $data['bill_current_date'],
            $data['bill_pdf_url'],
            date('Y-m-d')
        ]);

        return $success ? (int) $this->pdo->lastInsertId() : null;
    }

    public function inquireBill($title, $billID, $traceID) {
        $url = "https://core.inquiry.ayantech.ir/webservices/core.svc/{$title}BillInquiry";
        $token = "BBDE5FFA84EC4948BFEFDA53BDB6FC44";

        $payload = [
            "Identity" => [ "Token" => $token ],
            "Parameters" => [
                $title . "BillID" => $billID,
                "TraceNumber" => $traceID
            ]
        ];

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        file_put_contents("/home3/tzcddjfo/public_html/hoomPlus/app/logs/bill.txt",
            "[" . date('Y-m-d H:i:s') . "]\nPayload: " . json_encode($payload, JSON_PRETTY_PRINT) .
            "\nHTTP Code: $http_code\nCurl Error: $curl_error\nResponse: $response\n\n",
            FILE_APPEND
        );

        return $response ? json_decode($response, true) : null;
    }
}












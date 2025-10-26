<?php
declare(strict_types=1);
require_once '../app/Dbh.php';

class ReportModel{
    private $pdo;

    public function __construct(){
        $db = new Dbh();
        $this->pdo = $db->connect();
    }

    public function get_all_charges(): array|null{
        $sql = "SELECT charge.*, units.unit_number FROM charge INNER JOIN units ON charge.unit_id = units.id WHERE units.user_id = ? ORDER BY charge.status, created_at; ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_info']['id']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function get_unit_charges($unit_id): array|null{
        $sql = "SELECT charge.*, units.unit_number FROM charge INNER JOIN units ON charge.unit_id = units.id WHERE charge.unit_id = ? ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$unit_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function payed_charge($charge_id, $pay_type): array|bool  {
        $sql = "UPDATE charge SET charge.status = 1 and charge.pay_type = ? WHERE charge.id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$pay_type, $charge_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: ['error'];
    }
    public function payed_units($unit_id, $amount): array|bool  {
        $sql = "UPDATE units SET debt = debt - ? WHERE id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([ $amount, $unit_id ]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: ['error'];
    }
}
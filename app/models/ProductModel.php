<?php
declare(strict_types=1);
require_once 'app/Dbh.php';

class ProductModel{
    private $pdo;
    public function __construct(){
        $db = new Dbh();
        $this->pdo = $db->connect();
    }
    
    public function get_products(){
        $sql = "SELECT * FROM products ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }

    public function get_products_by_category($category){
        $sql = "SELECT LPAD(id, 3, '0') as id, title, price, description, image FROM products WHERE category = ? ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$category]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }

    public function add_product($title, $amount, $category, $description, $image){
        $sql = "INSERT INTO products (title, price, category, description, image) VALUES(?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$title, $amount, $category, $description, $image]); // image را هم اضافه کنید
        return true;
    }

    
    public function update_product($id, $title, $price, $description){
        $sql = "UPDATE products SET title = :title, price = :price, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':price' => $price,
            ':description' => $description
        ]);
    }
    
    public function delete_product($id){
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

}
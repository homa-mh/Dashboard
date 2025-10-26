<?php
declare(strict_types=1);
require_once 'app/models/ProductModel.php';

class ProductController{
    private $model;
    public function __construct(){
        $this->model = new ProductModel();
    }
    
    
    public function get_products(){
        return $this->model->get_products();
    }
    
    public function get_products_by_category($category){
        return $this->model->get_products_by_category($category);
    }
    
    public function add_product($title, $amount, $category, $description, $image = ''){
        try{
            $this->model->add_product($title, $amount, $category, $description, $image);
        }catch(PDOException $e){
            echo "error in insert product: ".$e->getMessage();
            exit();
        }
    }
    
    public function update_product($id, $title, $price, $description){
        try{
            $this->model->update_product($id, $title, $price, $description);
        }catch(PDOException $e){
            // به جای echo، throw کن
            throw new Exception("error in update product: ".$e->getMessage());
        }
    }

    
    public function delete_product($id){
        try{
            $this->model->delete_product($id);
        }catch(PDOException $e){
            echo "error in update product: ".$e->getMessage();
            exit();
        }
    }

}
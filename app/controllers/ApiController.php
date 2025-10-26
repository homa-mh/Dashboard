<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ApiModel.php';

class ApiController
{
    private $apiModel;

    public function __construct()
    {
        $this->apiModel = new ApiModel();
    }

    // Get all items
    public function get_items($num)
    {
        $this->apiModel = new ApiModel();
        return $this->apiModel->get_data("2027", (string)$num);
    }

    // Insert a new item
    // public function insert_item($data)
    // {
    //     return $this->apiModel->insertData("https://hivaind.ir/wil/insert81v3.php", $data);
    // }

    // // Update an item
    // public function update_item($id, $data)
    // {
    //     return $this->apiModel->updateData("https://hivaind.ir/wil/insert81v3.php", $id, $data);
    // }


}


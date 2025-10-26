<?php
declare(strict_types=1);
class ApiModel {
    private $baseUrl = "https://hivaind.ir/wil/logidjson81.php";

    // Function to fetch data
    public function get_data($id, $row) {
        $url = $this->baseUrl . "?id=" . urlencode($id) . "&row=" . urlencode($row);
        
        // Use cURL to make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check if request was successful
        if ($httpCode === 200) {
            return json_decode($response, true); // Convert JSON to array
        } else {
            return ["error" => "API request failed with status code $httpCode"];
        }
    }
}



<?php
header('Content-Type: application/json');
require_once('../config/dbcon.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if(isset($_GET['type'])) {
        $type = $_GET['type'];
        
        switch($type) {
            case 'provinces':
                $query = "SELECT * FROM provinces ORDER BY name";
                $result = mysqli_query($con, $query);
                if(!$result) {
                    throw new Exception("Database error: " . mysqli_error($con));
                }
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $data]);
                break;
                
            case 'districts':
                if(!isset($_GET['province_id'])) {
                    throw new Exception("Missing province_id parameter");
                }
                $province_id = mysqli_real_escape_string($con, $_GET['province_id']);
                $query = "SELECT * FROM districts WHERE province_id = '$province_id' ORDER BY name";
                $result = mysqli_query($con, $query);
                if(!$result) {
                    throw new Exception("Database error: " . mysqli_error($con));
                }
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $data]);
                break;
                
            case 'wards':
                if(!isset($_GET['district_id'])) {
                    throw new Exception("Missing district_id parameter");
                }
                $district_id = mysqli_real_escape_string($con, $_GET['district_id']);
                $query = "SELECT * FROM wards WHERE district_id = '$district_id' ORDER BY name";
                $result = mysqli_query($con, $query);
                if(!$result) {
                    throw new Exception("Database error: " . mysqli_error($con));
                }
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $data]);
                break;
                
            default:
                throw new Exception("Invalid type parameter");
        }
    } else {
        throw new Exception("Missing type parameter");
    }
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 
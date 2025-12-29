<?php
if (!defined('INCLUDED_MYFUNCTIONS')) {
    define('INCLUDED_MYFUNCTIONS', true);

    include(__DIR__ . '/../config/dbcon.php');

    if (!function_exists('getAdminAll')) {
        function getAdminAll($table)
        {
            global $con;
            $query = "SELECT * FROM $table ORDER BY id DESC";
            return mysqli_query($con, $query);
        }
    }

    if (!function_exists('getAdminByID')) {
        function getAdminByID($table, $id)
        {
            global $con;
            $query = "SELECT * FROM $table WHERE id='$id'";
            return mysqli_query($con, $query);
        }
    }

    if (!function_exists('getAllUsers')) {
        function getAllUsers($page = 0)
        {
            global $con;
            $query = "SELECT `users`.*, COUNT(`order_detail`.`id`) AS `total_buy` FROM `users`
                    LEFT JOIN `order_detail` ON `users`.`id` = `order_detail`.`user_id`
                    GROUP BY `users`.`id`
                    ORDER BY `users`.`creat_at` DESC";
            return mysqli_query($con, $query);
        }
    }

    // order functions
    if (!function_exists('getAllOrder')) {
        function getAllOrder($type = -1)
        {
            global $con;
            $getStatus = "1,2,3,4";
            if ($type != -1) {
                $getStatus = $type . "";
            }
            $query = "SELECT `orders`.*,COUNT(`order_detail`.`id`) as`quantity`,
                        `users`.`name`,`users`.`email`,`users`.`phone`,`users`.`address` FROM`orders`
                        JOIN `users` ON `orders`.`user_id` = `users`.`id`
                        LEFT JOIN `order_detail` ON `order_detail`.`order_id` = `orders`.`id`
                        WHERE`orders`.`status` IN($getStatus)
                        GROUP BY `orders`.`id`
                        ORDER BY `orders`.`id` DESC";
            return mysqli_query($con, $query);
        }
    }

    if (!function_exists('getOrderDetail')) {
        function getOrderDetail($order_id)
        {
            global $con;
            $query = "SELECT `users`.`name`,`users`.`email`,`users`.`phone`,`users`.`address`,
                        `products`.`name` as `name_product`, `products`.`selling_price`,`products`.`image`,
                        `order_detail`.*  FROM `order_detail` 
                        JOIN `users` ON `order_detail`.`user_id` = `users`.`id`
                        JOIN `products` ON `products`.`id` = `order_detail`.`product_id`
                        WHERE `order_id` = '$order_id'";
            return mysqli_query($con, $query);
        }
    }

    if (!function_exists('totalPriceGet')) {
        function totalPriceGet()
        {
            global $con;
            $query = "SELECT selling_price * quantity as price FROM `order_detail` WHERE `status` = 4";
            $prices = mysqli_query($con, $query);
            $total_price = 0;
            foreach ($prices as $price) {
                $total_price += $price['price'];
            }
            return $total_price;
        }
    }

    if (!function_exists('getAdminOrderHistory')) {
        function getAdminOrderHistory()
        {
            global $con;
            $query = "SELECT o.*, oi.* FROM orders o, order_items oi 
                    WHERE oi.order_id=o.id 
                    ORDER BY o.id DESC";
            return mysqli_query($con, $query);
        }
    }

    if (!function_exists('totalValue')) {
        function totalValue($table){
            global $con;
            $query = "SELECT COUNT(*) as `number` FROM $table";
            $totalValue = mysqli_query($con, $query);
            $totalValue = mysqli_fetch_array($totalValue);
            return $totalValue['number'];
        }
    }

    if (!function_exists('getAllOrders')) {
        function getAllOrders()
        {
            global $con;
            $query = "SELECT o.*, u.name as user_name, u.phone as user_phone, u.address as user_address 
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id 
                    ORDER BY o.created_at DESC";
            return mysqli_query($con, $query);
        }
    }

    if (!function_exists('getMonthlyRevenue')) {
        function getMonthlyRevenue($month, $year) {
            global $con;
            
            $query = "SELECT COALESCE(SUM(total_price), 0) as revenue 
                      FROM orders 
                      WHERE MONTH(created_at) = ? 
                      AND YEAR(created_at) = ? 
                      AND status = 'Completed'";
                      
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "ii", $month, $year);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            
            return $row['revenue'];
        }
    }

    if (!function_exists('getRevenueData')) {
        function getRevenueData() {
            $currentYear = date('Y');
            $currentMonth = date('n');
            $data = [];
            
            // Lấy dữ liệu 12 tháng gần nhất
            for ($i = 0; $i < 12; $i++) {
                $month = $currentMonth - $i;
                $year = $currentYear;
                
                if ($month <= 0) {
                    $month += 12;
                    $year--;
                }
                
                $revenue = getMonthlyRevenue($month, $year);
                $monthName = date('M', mktime(0, 0, 0, $month, 1));
                
                $data[] = [
                    'month' => $monthName,
                    'revenue' => $revenue,
                    'year' => $year
                ];
            }
            
            return array_reverse($data);
        }
    }

    if (!function_exists('formatCurrency')) {
        function formatCurrency($amount) {
            return number_format($amount, 0, ',', '.') . ' đ';
        }
    }

    if (!function_exists('getProductReviewStats')) {
        function getProductReviewStats($product_id) {
            global $con;
            
            // Initialize stats array with default values
            $stats = [
                'total_reviews' => 0,
                'average_rating' => 0,
                '1_star' => 0,
                '2_star' => 0,
                '3_star' => 0,
                '4_star' => 0,
                '5_star' => 0
            ];
            
            // Get total reviews and average rating
            $query = "SELECT 
                        COUNT(*) as total_reviews,
                        COALESCE(AVG(rating), 0) as average_rating,
                        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star,
                        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star
                    FROM reviews 
                    WHERE product_id = ?";
                    
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                $stats['total_reviews'] = (int)$row['total_reviews'];
                $stats['average_rating'] = (float)$row['average_rating'];
                $stats['1_star'] = (int)$row['one_star'];
                $stats['2_star'] = (int)$row['two_star'];
                $stats['3_star'] = (int)$row['three_star'];
                $stats['4_star'] = (int)$row['four_star'];
                $stats['5_star'] = (int)$row['five_star'];
            }
            
            return $stats;
        }
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $message)
    {
        $_SESSION['message'] = $message;
        header('Location: ' . $url);
        exit();
    }
}

if (!function_exists('getSlugActive')) {
    function getSlugActive($table, $slug)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE slug='$slug' AND status='0'";
        return mysqli_query($con, $query);
    }
}

if (!function_exists('getAllActive')) {
    function getAllActive($table)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE status='0'";
        return mysqli_query($con, $query);
    }
}

if (!function_exists('getIDActive')) {
    function getIDActive($table, $id)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE id='$id' AND status='0'";
        return mysqli_query($con, $query);
    }
}

if (!function_exists('getCartItems')) {
    function getCartItems()
    {
        global $con;
        $userId = $_SESSION['auth_user']['user_id'];
        $query = "SELECT c.id as cid, c.prod_id, c.prod_qty, p.id as pid, p.name, p.image, p.selling_price 
                FROM carts c, products p WHERE c.prod_id=p.id AND c.user_id='$userId' ORDER BY c.id DESC";
        return mysqli_query($con, $query);
    }
}

if (!function_exists('getOrders')) {
    function getOrders()
    {
        global $con;
        $userId = $_SESSION['auth_user']['user_id'];
        
        $query = "SELECT * FROM orders WHERE user_id='$userId' ORDER BY id DESC";
        return mysqli_query($con, $query);
    }
}

if (!function_exists('checkTrackingNoValid')) {
    function checkTrackingNoValid($trackingNo)
    {
        global $con;
        $userId = $_SESSION['auth_user']['user_id'];
        
        $query = "SELECT * FROM orders WHERE tracking_no='$trackingNo' AND user_id='$userId'";
        return mysqli_query($con, $query);
    }
}

if (!function_exists('getOrderHistory')) {
    function getOrderHistory()
    {
        global $con;
        $userId = $_SESSION['auth_user']['user_id'];
        
        $query = "SELECT o.*, oi.* FROM orders o, order_items oi 
                WHERE oi.order_id=o.id AND o.user_id='$userId' 
                ORDER BY o.id DESC";
        return mysqli_query($con, $query);
    }
}

if (!function_exists('getRecentOrders')) {
    function getRecentOrders($limit = 5) {
        global $con;
        $query = "SELECT o.*, u.name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT ?";
                
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
}

if (!function_exists('getOrderStatusClass')) {
    function getOrderStatusClass($status) {
        switch($status) {
            case 'Pending':
                return 'warning';
            case 'Processing':
                return 'info';
            case 'Completed':
                return 'success';
            case 'Cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}

if (!function_exists('getOrderStatusStats')) {
    function getOrderStatusStats() {
        global $con;
        $query = "SELECT status, COUNT(*) as count 
                FROM orders 
                GROUP BY status";
                
        $result = mysqli_query($con, $query);
        $stats = [];
        
        while($row = mysqli_fetch_assoc($result)) {
            $stats[] = [
                'status' => $row['status'],
                'count' => $row['count']
            ];
        }
        
        return $stats;
    }
}
?>
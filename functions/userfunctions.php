<?php
if (!defined('INCLUDED_USERFUNCTIONS')) {
    define('INCLUDED_USERFUNCTIONS', true);

    include(__DIR__ . '/../config/dbcon.php');

    function getAllActive($table)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE status='0'";
        return mysqli_query($con, $query);
    }

    function getIDActive($table, $id)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE id='$id' AND status='0'";
        return mysqli_query($con, $query);
    }

    function getByID($table, $id)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE id='$id'";
        return mysqli_query($con, $query);
    }

    function getAll($table)
    {
        global $con;
        $query = "SELECT * FROM $table";
        return mysqli_query($con, $query);
    }

    function getBySlug($table, $slug)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE slug='$slug'";
        return mysqli_query($con, $query);
    }

    function totalValue($table)
    {
        global $con;
        $query = "SELECT COUNT(*) as `number` FROM $table";
        $totalValue = mysqli_query($con, $query);
        $totalValue = mysqli_fetch_array($totalValue);
        return $totalValue['number'];
    }

    function getBestSelling($limit = 8)
    {
        global $con;
        $query = "SELECT p.*, COUNT(od.product_id) as total_sold 
                  FROM products p 
                  LEFT JOIN order_detail od ON p.id = od.product_id 
                  WHERE p.status='0' 
                  GROUP BY p.id 
                  ORDER BY total_sold DESC 
                  LIMIT $limit";
        return mysqli_query($con, $query);
    }

    function getLatestProducts($numberGet, $page = 0, $type = "", $search = "")
    {
        global $con;
        $page_extra = $numberGet * $page;

        if ($type != "") {
            $categoryId = getBySlug("categories", $type);
            $categoryId = mysqli_fetch_array($categoryId)['id'];
            $query = "SELECT * FROM `products` 
                      WHERE `name` LIKE '%$search%' AND `category_id` = '$categoryId'
                      ORDER BY `id` DESC 
                      LIMIT $numberGet OFFSET $page_extra";
        } else {
            $query = "SELECT * FROM `products` 
                      WHERE `name` LIKE '%$search%'
                      ORDER BY `id` DESC 
                      LIMIT $numberGet OFFSET $page_extra";
        }

        return mysqli_query($con, $query);
    }

    function getBlogs($page, $keyWold)
    {
        global $con;
        $page_extra = 10 * $page;
        $query = "SELECT * FROM `blog` 
                  WHERE `title` LIKE '%$keyWold%'
                  ORDER BY `id` DESC
                  LIMIT 10 OFFSET $page_extra";
        return mysqli_query($con, $query);
    }

    // Order-related functions
    function checkOrder($id_product)
    {
        global $con;
        $user_id = $_SESSION['auth_user']['id'];
        $query = "SELECT `status` FROM `order_detail` 
                  WHERE `product_id` = '$id_product' AND `user_id` = '$user_id' AND `status` != 0 
                  ORDER BY `status`";
        $checkOrder = mysqli_query($con, $query);
        if (mysqli_num_rows($checkOrder)) {
            return mysqli_fetch_array($checkOrder)['status'];
        } else {
            return 0;
        }
    }

    function getMyOrders()
    {
        global $con;
        $user_id = $_SESSION['auth_user']['id'];
        $query = "SELECT `order_detail`.*, `products`.`name`, `products`.`slug` 
                  FROM `order_detail` 
                  JOIN `products` ON `order_detail`.`product_id` = `products`.`id`
                  WHERE `order_detail`.`user_id` = '$user_id' AND `order_detail`.`status` = 1";
        return mysqli_query($con, $query);
    }

    function getMyOrderVote($id)
    {
        global $con;
        $user_id = $_SESSION['auth_user']['id'];
        $query = "SELECT `order_detail`.*, `products`.`name`, `products`.`description`, 
                         `products`.`small_description`, `products`.`image`, `products`.`slug` 
                  FROM `order_detail` 
                  JOIN `products` ON `order_detail`.`product_id` = `products`.`id`
                  WHERE `order_detail`.`id` = '$id' 
                    AND `order_detail`.`status` = 4 
                    AND `order_detail`.`user_id` = $user_id";
        return mysqli_query($con, $query);
    }

    function getOrderWasBuy()
    {
        global $con;
        $userId = $_SESSION['auth_user']['id'];
        
        $query = "SELECT o.id as order_id, o.status, o.created_at, o.total_price,
                         p.id as product_id, p.name, p.slug, p.image, p.selling_price,
                         od.quantity
                  FROM orders o
                  JOIN order_detail od ON o.id = od.order_id
                  JOIN products p ON od.product_id = p.id
                  WHERE o.user_id = ?
                  ORDER BY o.created_at DESC";
                  
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    function getRate($product_id)
    {
        global $con;
        $query = "SELECT `order_detail`.*, `users`.`name` 
                  FROM `order_detail` 
                  JOIN `users` ON `order_detail`.`user_id` = `users`.`id`
                  WHERE `order_detail`.`product_id` = '$product_id' 
                    AND `order_detail`.`status` = 4 
                    AND `order_detail`.`rate` > 0";
        return mysqli_query($con, $query);
    }

    function avgRate($product_id)
    {
        global $con;
        $query = "SELECT AVG(`rate`) as `avg_rate` 
                  FROM `order_detail` 
                  WHERE `product_id` = '$product_id' 
                    AND `status` = 4 
                    AND `rate` > 0";
        $rate = mysqli_query($con, $query);
        $rate = mysqli_fetch_array($rate);
        return round($rate['avg_rate'], 1);
    }

    function redirect($url, $message)
    {
        $_SESSION['message'] = $message;
        header('Location: ' . $url);
        exit();
    }

    function getAllTrending()
    {
        global $con;
        $query = "SELECT * FROM products WHERE trending='1' AND status='0' ORDER BY id DESC LIMIT 10";
        return mysqli_query($con, $query);
    }

    function getNewProducts()
    {
        global $con;
        $query = "SELECT * FROM products WHERE status='0' ORDER BY id DESC LIMIT 8";
        return mysqli_query($con, $query);
    }

    function hasUserPurchased($product_id, $user_id)
    {
        global $con;
        
        // Kiểm tra trong bảng orders và order_detail
        $query = "SELECT COUNT(*) as purchase_count 
                 FROM orders o 
                 INNER JOIN order_detail od ON o.id = od.order_id 
                 WHERE o.user_id = ? 
                 AND od.product_id = ? 
                 AND o.status = 3";  // Status 3 = Completed
                 
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['purchase_count'] > 0;
    }

    function hasUserReviewed($product_id, $user_id)
    {
        global $con;
        $query = "SELECT COUNT(*) as review_count 
                  FROM product_reviews 
                  WHERE product_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['review_count'] > 0;
    }

    function submitReview($product_id, $user_id, $rating, $comment)
    {
        global $con;
        if (hasUserReviewed($product_id, $user_id)) {
            return false;
        }
        $query = "INSERT INTO product_reviews (product_id, user_id, rating, comment) 
                  VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "iiis", $product_id, $user_id, $rating, $comment);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy thông tin đánh giá của sản phẩm
     */
    function getProductReviews($product_id, $filters = []) {
        global $con;
        
        $query = "SELECT r.*, 
            u.name as reviewer_name, 
            u.avatar as reviewer_avatar,
            (SELECT COUNT(*) FROM review_helpful WHERE review_id = r.id) as helpful_count,
            (SELECT COUNT(*) FROM orders o 
             JOIN order_items oi ON o.id = oi.order_id 
             WHERE o.user_id = r.user_id AND oi.product_id = r.product_id AND o.status = 'Completed') as has_purchased
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ?";
        
        $params = [$product_id];
        $types = "i";
        
        // Apply filters
        if (!empty($filters)) {
            if (isset($filters['rating'])) {
                $query .= " AND r.rating = ?";
                $params[] = $filters['rating'];
                $types .= "i";
            }
            if (isset($filters['has_images']) && $filters['has_images']) {
                $query .= " AND r.images IS NOT NULL AND r.images != ''";
            }
            if (isset($filters['verified_purchase']) && $filters['verified_purchase']) {
                $query .= " AND has_purchased > 0";
            }
            if (isset($filters['sort'])) {
                switch ($filters['sort']) {
                    case 'newest':
                        $query .= " ORDER BY r.created_at DESC";
                        break;
                    case 'oldest':
                        $query .= " ORDER BY r.created_at ASC";
                        break;
                    case 'highest_rating':
                        $query .= " ORDER BY r.rating DESC";
                        break;
                    case 'lowest_rating':
                        $query .= " ORDER BY r.rating ASC";
                        break;
                    case 'most_helpful':
                        $query .= " ORDER BY helpful_count DESC";
                        break;
                }
            } else {
                $query .= " ORDER BY r.created_at DESC";
            }
        }
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $reviews = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['images']) {
                $row['images'] = explode(',', $row['images']);
            } else {
                $row['images'] = [];
            }
            $reviews[] = $row;
        }
        
        return $reviews;
    }

    /**
     * Lấy thống kê đánh giá của sản phẩm
     */
    function getProductReviewStats($product_id) {
        global $con;
        
        // Initialize default stats
        $stats = [
            'total_reviews' => 0,
            'average_rating' => 0,
            'five_star' => 0,
            'four_star' => 0,
            'three_star' => 0,
            'two_star' => 0,
            'one_star' => 0,
            'with_images' => 0,
            'verified_purchases' => 0
        ];
        
        $query = "SELECT 
            COUNT(*) as total_reviews,
            COALESCE(AVG(rating), 0) as average_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star,
            SUM(CASE WHEN images IS NOT NULL AND images != '' THEN 1 ELSE 0 END) as with_images,
            (SELECT COUNT(DISTINCT r.id) 
             FROM reviews r 
             JOIN orders o ON o.user_id = r.user_id 
             JOIN order_items oi ON o.id = oi.order_id 
             WHERE r.product_id = ? AND oi.product_id = r.product_id AND o.status = 'Completed') as verified_purchases
            FROM reviews 
            WHERE product_id = ?";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $product_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                // Convert null values to 0 and ensure integer types
                $stats['total_reviews'] = (int)$row['total_reviews'];
                $stats['average_rating'] = round((float)$row['average_rating'], 1);
                $stats['five_star'] = (int)($row['five_star'] ?? 0);
                $stats['four_star'] = (int)($row['four_star'] ?? 0);
                $stats['three_star'] = (int)($row['three_star'] ?? 0);
                $stats['two_star'] = (int)($row['two_star'] ?? 0);
                $stats['one_star'] = (int)($row['one_star'] ?? 0);
                $stats['with_images'] = (int)($row['with_images'] ?? 0);
                $stats['verified_purchases'] = (int)($row['verified_purchases'] ?? 0);
            }
        }
        
        return $stats;
    }

    /**
     * Thêm đánh giá mới
     */
    function addReview($user_id, $product_id, $rating, $comment, $images = []) {
        global $con;
        
        // Kiểm tra xem người dùng đã mua sản phẩm chưa
        $check_purchase = "SELECT COUNT(*) as count 
            FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Completed'";
        
        $stmt = mysqli_prepare($con, $check_purchase);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $has_purchased = mysqli_fetch_assoc($result)['count'] > 0;
        
        if (!$has_purchased) {
            return ['status' => 'error', 'message' => 'Bạn cần mua sản phẩm trước khi đánh giá'];
        }
        
        // Kiểm tra xem đã đánh giá chưa
        $check_review = "SELECT COUNT(*) as count FROM reviews WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($con, $check_review);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_fetch_assoc($result)['count'] > 0) {
            return ['status' => 'error', 'message' => 'Bạn đã đánh giá sản phẩm này rồi'];
        }
        
        // Xử lý upload ảnh
        $image_paths = [];
        if (!empty($images)) {
            foreach ($images['tmp_name'] as $key => $tmp_name) {
                $file_name = $images['name'][$key];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_name = uniqid() . '.' . $file_ext;
                $upload_path = '../uploads/reviews/' . $new_name;
                
                if (move_uploaded_file($tmp_name, $upload_path)) {
                    $image_paths[] = 'uploads/reviews/' . $new_name;
                }
            }
        }
        
        // Thêm đánh giá
        $query = "INSERT INTO reviews (user_id, product_id, rating, comment, images, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $images_str = !empty($image_paths) ? implode(',', $image_paths) : null;
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "iisss", $user_id, $product_id, $rating, $comment, $images_str);
        
        if (mysqli_stmt_execute($stmt)) {
            return ['status' => 'success', 'message' => 'Đánh giá của bạn đã được ghi nhận'];
        } else {
            return ['status' => 'error', 'message' => 'Có lỗi xảy ra, vui lòng thử lại'];
        }
    }

    /**
     * Đánh dấu đánh giá là hữu ích
     */
    function markReviewHelpful($user_id, $review_id) {
        global $con;
        
        // Kiểm tra xem đã đánh dấu chưa
        $check_query = "SELECT id FROM review_helpful WHERE user_id = ? AND review_id = ?";
        $stmt = mysqli_prepare($con, $check_query);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $review_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Nếu đã đánh dấu thì xóa
            $delete_query = "DELETE FROM review_helpful WHERE user_id = ? AND review_id = ?";
            $stmt = mysqli_prepare($con, $delete_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $review_id);
            mysqli_stmt_execute($stmt);
            
            return ['status' => 'success', 'action' => 'removed'];
        } else {
            // Nếu chưa đánh dấu thì thêm mới
            $insert_query = "INSERT INTO review_helpful (user_id, review_id, created_at) VALUES (?, ?, NOW())";
            $stmt = mysqli_prepare($con, $insert_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $review_id);
            mysqli_stmt_execute($stmt);
            
            return ['status' => 'success', 'action' => 'added'];
        }
    }

    function tableExists($table)
    {
        global $con;
        $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
        return mysqli_num_rows($result) > 0;
    }

    function getAverageRating($product_id)
    {
        global $con;
        if (!tableExists('product_reviews')) return 0;

        $query = "SELECT AVG(rating) as avg_rating 
                  FROM product_reviews 
                  WHERE product_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
    }

    function getSlugActive($table, $slug)
    {
        global $con;
        $query = "SELECT * FROM $table WHERE slug='$slug' AND status='0' LIMIT 1";
        return mysqli_query($con, $query);
    }

    function getProdByCategory($category_id)
    {
        global $con;
        $category_id = mysqli_real_escape_string($con, $category_id);
        
        // Lấy sản phẩm theo category_id
        $query = "SELECT * FROM products 
                 WHERE category_id = '$category_id' 
                 AND status = '0' 
                 ORDER BY id DESC";
                 
        return mysqli_query($con, $query);
    }

    function getCartItems()
    {
        global $con;
        
        // Kiểm tra xem bảng carts có tồn tại không
        $check_table = mysqli_query($con, "SHOW TABLES LIKE 'carts'");
        if(mysqli_num_rows($check_table) == 0) {
            // Nếu bảng chưa tồn tại, tạo bảng mới
            $create_table = "CREATE TABLE IF NOT EXISTS `carts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `prod_id` int(11) NOT NULL,
                `prod_qty` int(11) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `prod_id` (`prod_id`),
                CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`prod_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            
            mysqli_query($con, $create_table);
        }

        if(isset($_SESSION['auth']) && isset($_SESSION['auth_user']['id']))
        {
            $userId = $_SESSION['auth_user']['id'];
            $query = "SELECT c.id as cid, c.prod_id, c.prod_qty, p.id as pid, p.name, p.image, p.selling_price 
                    FROM carts c, products p WHERE c.prod_id=p.id AND c.user_id='$userId' ORDER BY c.id DESC";
            return mysqli_query($con, $query);
        }
        else
        {
            return mysqli_query($con, "SELECT 1 FROM carts WHERE 1=0"); // Return empty result set
        }
    }

    function getOrders($userId = null)
    {
        global $con;
        if($userId === null && isset($_SESSION['auth_user']['id'])) {
            $userId = $_SESSION['auth_user']['id'];
        }
        
        if($userId) {
            $query = "SELECT * FROM orders WHERE user_id='$userId' ORDER BY id DESC";
            return mysqli_query($con, $query);
        }
        
        return mysqli_query($con, "SELECT 1 FROM orders WHERE 1=0"); // Return empty result set
    }

    function checkTrackingNoValid($trackingNo)
    {
        global $con;
        $userId = $_SESSION['auth_user']['id'];
        $query = "SELECT * FROM orders WHERE tracking_no='$trackingNo' AND user_id='$userId'";
        return mysqli_query($con, $query);
    }

    function getOrderItems($orderId) {
        global $con;
        $query = "SELECT oi.*, p.name, p.image 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    // Lấy tổng số đánh giá cho sản phẩm
    function getTotalReviews($product_id) {
        global $con;
        $query = "SELECT COUNT(*) as total FROM reviews WHERE product_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        return $data['total'];
    }

    // Lấy số lượng đánh giá cho từng số sao
    function getRatingCounts($product_id) {
        global $con;
        $counts = array();
        $query = "SELECT rating, COUNT(*) as count FROM reviews WHERE product_id = ? GROUP BY rating";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while($row = mysqli_fetch_assoc($result)) {
            $counts[$row['rating']] = $row['count'];
        }
        return $counts;
    }

    // Cập nhật số lượt đánh giá hữu ích
    function updateHelpfulCount($review_id, $user_id, $action) {
        global $con;
        
        if($action === 'add') {
            $query = "INSERT INTO review_helpful (review_id, user_id) VALUES (?, ?)";
        } else {
            $query = "DELETE FROM review_helpful WHERE review_id = ? AND user_id = ?";
        }
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ii", $review_id, $user_id);
        return mysqli_stmt_execute($stmt);
    }

    // Kiểm tra xem người dùng đã đánh dấu đánh giá là hữu ích chưa
    function hasMarkedHelpful($review_id, $user_id) {
        global $con;
        $query = "SELECT 1 FROM review_helpful WHERE review_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ii", $review_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
}

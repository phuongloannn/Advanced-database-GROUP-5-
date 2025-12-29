<?php
// Include header.php first as it already includes necessary files and starts the session
include("./includes/header.php");
include("./functions/userfunctions.php");
?>
<link rel="stylesheet" href="css/product-gallery.css">

<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get product average rating
function getProductAverageRating($product_id) {
    global $con;
    $query = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
}

// Function to safely get GET parameter
function getSlugParameter() {
    return isset($_GET['slug']) ? trim($_GET['slug']) : '';
}

// Function to validate product data
function validateProduct($product) {
    return isset($product['id']) && 
           isset($product['name']) && 
           isset($product['selling_price']) && 
           isset($product['qty']);
}

$slug = getSlugParameter();

if (!empty($slug)) {
    global $con;
    $slug = mysqli_real_escape_string($con, $slug);
    $product_query = "SELECT p.id, p.name, p.slug, p.small_description, 
                            p.description, p.original_price, p.selling_price, 
                            p.image, p.qty, p.status, p.trending,
                            c.name as cname, c.slug as category_slug
                     FROM products p 
                     LEFT JOIN categories c ON c.id = p.category_id 
                     WHERE p.slug = ? AND p.status = '0'";
    
    // Use prepared statement
    $stmt = mysqli_prepare($con, $product_query);
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_array($result);
        
        if (validateProduct($product)) {
            ?>
            <link rel="stylesheet" href="css/product-gallery.css">
            <style>
                /* CSS Variables */
                :root {
                    --primary-color: #3d5af1;
                    --secondary-color: #0b1e6b;
                    --accent-color: #ffe600;
                    --text-color: #333;
                    --muted-color: #555;
                    --bg-color: #f9f9f9;
                    --card-bg: #fff;
                    --border-color: #dee2e6;
                    --star-color: #ffc107;
                    --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    --transition: all 0.3s ease;
                }

                /* General Styles */
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    line-height: 1.6;
                    color: var(--text-color);
                    background-color: var(--bg-color);
                    overflow-x: hidden;
                }

                .container {
                    max-width: 1400px;
                    margin: 0 auto;
                    padding: 0 15px;
                }

                /* Breadcrumb */
                .breadcrumb-bar {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                    padding: 12px 0;
                    z-index: 1000;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                }

                .breadcrumb-bar h6 {
                    font-size: 15px;
                    font-weight: 600;
                    margin: 0;
                    color: #fff;
                }

                .breadcrumb-bar a {
                    color: #fff;
                    text-decoration: none;
                    transition: var(--transition);
                }

                .breadcrumb-bar a:hover {
                    color: var(--accent-color);
                }

                /* Product Section */
                .product-section {
                    padding: 80px 0 40px;
                    background: var(--card-bg);
                    margin-top: 60px;
                }

                .product-data {
                    display: flex;
                    gap: 20px;
                    background: var(--card-bg);
                    border-radius: 12px;
                    box-shadow: var(--shadow);
                    padding: 25px;
                }

                .product-image-container {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                    margin-bottom: 20px;
                    width: 100%;
                }

                .main-image {
                    width: 100%;
                    position: relative;
                    overflow: hidden;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    background: #fff;
                    aspect-ratio: 4/3;
                    margin-bottom: 20px;
                }

                .main-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    display: block;
                    transition: opacity 0.3s ease;
                }

                .thumbnail-container {
                    width: 100%;
                    overflow-x: auto;
                    padding: 10px 0;
                    scrollbar-width: thin;
                    -ms-overflow-style: none;
                }

                .thumbnail-track {
                    display: flex;
                    gap: 15px;
                    padding: 5px;
                }

                .thumbnail-image {
                    width: 100px;
                    height: 100px;
                    object-fit: cover;
                    border-radius: 8px;
                    cursor: pointer;
                    border: 2px solid transparent;
                    transition: all 0.3s ease;
                    flex-shrink: 0;
                    background: #fff;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }

                .thumbnail-image:hover {
                    transform: scale(1.1);
                    border-color: #3d5af1;
                    box-shadow: 0 4px 15px rgba(61, 90, 241, 0.2);
                }

                .thumbnail-image.active {
                    border-color: #3d5af1;
                    box-shadow: 0 4px 15px rgba(61, 90, 241, 0.3);
                    transform: scale(1.05);
                }

                /* Hide scrollbar but keep functionality */
                .thumbnail-container::-webkit-scrollbar {
                    height: 6px;
                }

                .thumbnail-container::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 3px;
                }

                .thumbnail-container::-webkit-scrollbar-thumb {
                    background: #888;
                    border-radius: 3px;
                }

                .thumbnail-container::-webkit-scrollbar-thumb:hover {
                    background: #555;
                }

                .product-details {
                    flex: 1.5;
                }

                .product-details h4 {
                    font-size: 2rem;
                    color: var(--secondary-color);
                    font-weight: 700;
                    margin-bottom: 15px;
                    line-height: 1.3;
                }

                .product-details hr {
                    border-color: var(--border-color);
                    margin: 20px 0;
                }

                .price-info {
                    display: flex;
                    gap: 20px;
                    margin-bottom: 20px;
                }

                .price-info h5 {
                    font-size: 1.3rem;
                    font-weight: 600;
                }

                .selling-price {
                    color: #2e7d32;
                }

                .original-price {
                    color: #d32f2f;
                    text-decoration: line-through;
                }

                .product-actions {
                    display: flex;
                    align-items: center;
                    gap: 20px;
                    margin-bottom: 20px;
                }

                .input-group {
                    width: 150px;
                    border: 1px solid var(--border-color);
                    border-radius: 8px;
                    overflow: hidden;
                }

                .input-group-text {
                    background: var(--bg-color);
                    border: none;
                    padding: 10px;
                    cursor: pointer;
                    transition: var(--transition);
                    font-size: 1.2rem;
                    color: var(--primary-color);
                }

                .input-group-text:hover {
                    background: #e9ecef;
                }

                .input-qty {
                    border: none;
                    text-align: center;
                    font-size: 1.1rem;
                    background: var(--card-bg);
                }

                .btn-primary {
                    background: var(--primary-color);
                    border: none;
                    padding: 12px 30px;
                    border-radius: 25px;
                    font-weight: 600;
                    font-size: 1rem;
                    color: #fff;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: var(--transition);
                }

                .btn-primary:hover {
                    background: var(--secondary-color);
                    transform: translateY(-3px);
                    box-shadow: 0 4px 10px rgba(11, 30, 107, 0.3);
                }

                .stock-info {
                    font-size: 0.9rem;
                    color: var(--muted-color);
                    margin-top: 5px;
                }

                .product-description {
                    font-size: 1rem;
                    color: var(--muted-color);
                    line-height: 1.8;
                    margin-top: 20px;
                }

                /* Review Section */
                .reviews-section {
                    margin-top: 40px;
                    padding: 25px;
                    background: var(--bg-color);
                    border-radius: 12px;
                    box-shadow: var(--shadow);
                }

                .reviews-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 25px;
                }

                .reviews-header h3 {
                    font-size: 1.8rem;
                    color: var(--secondary-color);
                    font-weight: 700;
                }

                .rating-summary {
                    display: flex;
                    gap: 30px;
                    padding: 20px;
                    background: var(--card-bg);
                    border-radius: 12px;
                    box-shadow: var(--shadow);
                    margin-bottom: 25px;
                }

                .average-rating {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }

                .rating-circle {
                    width: 80px;
                    height: 80px;
                    background: conic-gradient(var(--primary-color) calc(var(--rating-percent) * 3.6deg), #e9ecef 0deg);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                }

                .rating-circle::before {
                    content: '';
                    position: absolute;
                    width: 70px;
                    height: 70px;
                    background: var(--card-bg);
                    border-radius: 50%;
                }

                .rating-number {
                    font-size: 1.8rem;
                    color: var(--primary-color);
                    font-weight: bold;
                    z-index: 1;
                }

                .rating-details {
                    display: flex;
                    flex-direction: column;
                    gap: 5px;
                }

                .rating-stars i {
                    font-size: 1.3rem;
                    color: var(--star-color);
                }

                .rating-stars i:not(.active) {
                    color: #e4e5e7;
                }

                .rating-count {
                    font-size: 0.95rem;
                    color: var(--muted-color);
                }

                .rating-bars {
                    flex: 1;
                }

                .rating-bar-item {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    margin-bottom: 8px;
                }

                .rating-bar-label {
                    width: 60px;
                    font-size: 0.95rem;
                    font-weight: 600;
                }

                .rating-bar-bg {
                    flex: 1;
                    background: #e9ecef;
                    border-radius: 12px;
                    height: 12px;
                }

                .rating-bar-fill {
                    background: var(--primary-color);
                    height: 100%;
                    border-radius: 12px;
                    transition: width 0.5s ease;
                }

                .rating-bar-count {
                    font-size: 0.95rem;
                    color: var(--muted-color);
                    width: 40px;
                    text-align: right;
                }

                .review-filters {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-bottom: 25px;
                }

                .filter-button {
                    background: var(--card-bg);
                    border: 1px solid var(--border-color);
                    padding: 10px 20px;
                    border-radius: 25px;
                    font-size: 0.95rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: var(--transition);
                }

                .filter-button:hover {
                    background: #f1f3f5;
                    border-color: var(--primary-color);
                }

                .filter-button.active {
                    background: var(--primary-color);
                    color: #fff;
                    border-color: var(--primary-color);
                }

                .review-item {
                    padding: 20px;
                    background: var(--card-bg);
                    border-radius: 12px;
                    margin-bottom: 15px;
                    box-shadow: var(--shadow);
                    transition: var(--transition);
                }

                .review-item:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
                }

                .reviewer-info {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    margin-bottom: 15px;
                }

                .reviewer-avatar {
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    overflow: hidden;
                    background: #f0f0f0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 15px;
                }

                .reviewer-avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .avatar-placeholder {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #3d5af1;
                    color: white;
                    font-weight: bold;
                    font-size: 18px;
                }

                .reviewer-name {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                    color: #333;
                }

                .review-date {
                    margin: 0;
                    font-size: 14px;
                    color: #666;
                }

                .verified-badge {
                    display: inline-flex;
                    align-items: center;
                    background: #e8f5e9;
                    color: #2e7d32;
                    padding: 2px 8px;
                    border-radius: 12px;
                    font-size: 12px;
                    margin-left: 8px;
                }

                .verified-badge i {
                    font-size: 12px;
                    margin-right: 4px;
                }

                .review-rating {
                    margin-bottom: 10px;
                }

                .review-rating i {
                    font-size: 1.1rem;
                    color: var(--star-color);
                }

                .review-rating i:not(.active) {
                    color: #e4e5e7;
                }

                .review-content {
                    font-size: 0.95rem;
                    color: var(--muted-color);
                    line-height: 1.7;
                    margin-bottom: 15px;
                }

                .review-images {
                    display: flex;
                    gap: 10px;
                    flex-wrap: wrap;
                }

                .review-image {
                    width: 90px;
                    height: 90px;
                    object-fit: cover;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: var(--transition);
                }

                .review-image:hover {
                    transform: scale(1.05);
                    box-shadow: var(--shadow);
                }

                .review-actions {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    font-size: 0.9rem;
                    color: var(--muted-color);
                }

                .helpful-btn {
                    background: none;
                    border: none;
                    color: var(--primary-color);
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    font-weight: 600;
                    transition: var(--transition);
                }

                .helpful-btn:hover {
                    color: var(--secondary-color);
                }

                .review-reply {
                    margin-top: 15px;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }

                .shop-reply-badge {
                    background: #e3f2fd;
                    color: #0288d1;
                    padding: 5px 10px;
                    border-radius: 15px;
                    font-size: 0.85rem;
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    margin-bottom: 10px;
                }

                /* Review Form */
                .review-form-section {
                    padding: 40px 0;
                    background: var(--bg-color);
                }

                .review-form {
                    padding: 25px;
                    background: var(--card-bg);
                    border-radius: 12px;
                    box-shadow: var(--shadow);
                }

                .review-form h3 {
                    font-size: 1.8rem;
                    color: var(--secondary-color);
                    font-weight: 700;
                    margin-bottom: 20px;
                }

                .star-rating {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin-bottom: 20px;
                }

                .rating-star {
                    font-size: 1.8rem;
                    color: #e4e5e7;
                    cursor: pointer;
                    transition: var(--transition);
                }

                .rating-star:hover,
                .rating-star.hover,
                .rating-star.active {
                    color: var(--star-color);
                    transform: scale(1.15);
                }

                .rating-text {
                    font-size: 1rem;
                    color: var(--muted-color);
                    font-weight: 600;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-label {
                    font-size: 1rem;
                    font-weight: 600;
                    color: var(--secondary-color);
                    margin-bottom: 8px;
                    display: block;
                }

                .form-control {
                    border: 1px solid var(--border-color);
                    border-radius: 8px;
                    padding: 12px;
                    font-size: 0.95rem;
                    width: 100%;
                    transition: var(--transition);
                }

                .form-control:focus {
                    border-color: var(--primary-color);
                    box-shadow: 0 0 0 3px rgba(61, 90, 241, 0.1);
                    outline: none;
                }

                .image-upload {
                    display: flex;
                    gap: 10px;
                    flex-wrap: wrap;
                }

                .upload-preview {
                    width: 90px;
                    height: 90px;
                    border: 2px dashed var(--border-color);
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    background-size: cover;
                    background-position: center;
                    transition: var(--transition);
                }

                .upload-preview:hover {
                    border-color: var(--primary-color);
                    transform: scale(1.05);
                }

                .submit-btn {
                    background: var(--primary-color);
                    color: #fff;
                    padding: 12px 30px;
                    border-radius: 25px;
                    border: none;
                    font-weight: 600;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: var(--transition);
                }

                .submit-btn:hover {
                    background: var(--secondary-color);
                    transform: translateY(-3px);
                    box-shadow: 0 4px 10px rgba(11, 30, 107, 0.3);
                }

                /* Image Preview Modal */
                .modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.85);
                    z-index: 1000;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }

                .modal-content {
                    max-width: 90%;
                    max-height: 90%;
                    border-radius: 12px;
                    transition: transform 0.3s ease;
                    transform: scale(0.95);
                }

                .modal.active .modal-content {
                    transform: scale(1);
                }

                .close {
                    position: absolute;
                    top: 20px;
                    right: 30px;
                    color: #fff;
                    font-size: 2.5rem;
                    cursor: pointer;
                    transition: var(--transition);
                }

                .close:hover {
                    color: var(--accent-color);
                }

                /* Responsive Design */
                @media (max-width: 1200px) {
                    .container {
                        max-width: 960px;
                    }

                    #mainImage {
                        height: 400px;
                    }
                }

                @media (max-width: 992px) {
                    .product-data {
                        flex-direction: column;
                        padding: 20px;
                    }

                    .product-images {
                        flex-direction: column;
                        align-items: center;
                    }

                    .thumbnail-list {
                        flex-direction: row;
                        width: 100%;
                        justify-content: center;
                    }

                    .thumbnail-image {
                        width: 70px;
                        height: 70px;
                    }

                    #mainImage {
                        height: 350px;
                    }

                    .rating-summary {
                        flex-direction: column;
                        gap: 20px;
                    }
                }

                @media (max-width: 768px) {
                    .container {
                        max-width: 540px;
                    }

                    .breadcrumb-bar {
                        position: relative;
                    }

                    .product-section {
                        margin-top: 0;
                        padding: 60px 0 30px;
                    }

                    .product-data {
                        padding: 15px;
                    }

                    #mainImage {
                        height: 300px;
                    }

                    .thumbnail-image {
                        width: 60px;
                        height: 60px;
                    }

                    .product-details h4 {
                        font-size: 1.8rem;
                    }

                    .reviews-header {
                        flex-direction: column;
                        gap: 15px;
                    }

                    .review-item {
                        padding: 15px;
                    }

                    .review-image,
                    .upload-preview {
                        width: 80px;
                        height: 80px;
                    }

                    .main-image {
                        aspect-ratio: 4/3;
                    }
                }

                @media (max-width: 576px) {
                    .container {
                        padding: 0 10px;
                    }

                    .product-data {
                        padding: 10px;
                    }

                    .btn-primary,
                    .submit-btn {
                        width: 100%;
                        padding: 10px;
                        font-size: 0.95rem;
                    }

                    .input-group {
                        width: 130px;
                    }

                    .reviews-section,
                    .review-form {
                        padding: 15px;
                    }

                    .product-details h4 {
                        font-size: 1.5rem;
                    }

                    .reviews-header h3,
                    .review-form h3 {
                        font-size: 1.5rem;
                    }

                    #mainImage {
                        height: 250px;
                    }

                    .thumbnail-image {
                        width: 50px;
                        height: 50px;
                    }
                }

                /* CSS cho phần upload ảnh */
                .image-upload-container {
                    margin: 15px 0;
                }
                
                .image-upload-preview {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-top: 10px;
                }
                
                .upload-box {
                    width: 100px;
                    height: 100px;
                    border: 2px dashed #ddd;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    background: #f8f9fa;
                }
                
                .upload-box:hover {
                    border-color: #0d6efd;
                    background: #e9ecef;
                }
                
                .upload-box i {
                    font-size: 24px;
                    color: #6c757d;
                }
                
                .preview-item {
                    position: relative;
                    width: 100px;
                    height: 100px;
                    border-radius: 8px;
                    overflow: hidden;
                }
                
                .preview-item img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                
                .remove-image {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                    background: rgba(255, 255, 255, 0.8);
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    font-size: 12px;
                    color: #dc3545;
                    border: none;
                    padding: 0;
                }
                
                .remove-image:hover {
                    background: rgba(255, 255, 255, 1);
                    color: #bb2d3b;
                }
                
                .upload-instructions {
                    font-size: 0.875rem;
                    color: #6c757d;
                    margin-top: 5px;
                }
                
                .image-upload-error {
                    color: #dc3545;
                    font-size: 0.875rem;
                    margin-top: 5px;
                    display: none;
                }
            </style>

            <div class="breadcrumb-bar">
                <div class="container">
                    <h6>
                        <a href="index.php">Trang chủ</a> / 
                        <a href="category.php">Danh mục</a> / 
                        <a href="category.php?slug=<?= htmlspecialchars($product['category_slug']) ?>"><?= htmlspecialchars($product['cname']) ?></a> /
                        <?= htmlspecialchars($product['name']) ?>
                    </h6>
                </div>
            </div>

            <div class="product-section">
                <div class="container product-data">
                    <div class="col-md-6">
                        <div class="product-image-container">
                            <div class="main-image">
                                <?php
                                // Get the base name of the main image (e.g., "dua3.jpg")
                                $main_image_base = basename($product['image']);
                                $main_image_name = pathinfo($main_image_base, PATHINFO_FILENAME);
                                
                                // Generate additional image names
                                $additional_images = array();
                                for($i = 1; $i <= 3; $i++) {
                                    $additional_images[] = $main_image_name . "-" . $i . ".jpg";
                                }
                                ?>
                                <img src="anh_xedap/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="mainImage" style="opacity: 1;">
                            </div>
                            <div class="thumbnail-container">
                                <div class="thumbnail-track">
                                    <!-- Main image thumbnail -->
                                    <img src="anh_xedap/<?= htmlspecialchars($product['image']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="thumbnail-image active"
                                         data-src="anh_xedap/<?= htmlspecialchars($product['image']) ?>"
                                         onclick="changeMainImage(this)"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                    
                                    <?php
                                    // Display additional images
                                    foreach($additional_images as $add_image) {
                                        $image_path = "anh_xedap/" . $add_image;
                                        if(file_exists($image_path)) {
                                            echo '<img src="' . htmlspecialchars($image_path) . '" 
                                                      alt="' . htmlspecialchars($product['name']) . '" 
                                                      class="thumbnail-image"
                                                      data-src="' . htmlspecialchars($image_path) . '"
                                                      onclick="changeMainImage(this)"
                                                      style="width: 80px; height: 80px; object-fit: cover;">';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <script>
                        function changeMainImage(element) {
                            const mainImage = document.getElementById('mainImage');
                            const thumbnails = document.querySelectorAll('.thumbnail-image');
                            
                            // Start fade out
                            mainImage.style.opacity = '0';
                            
                            // Change source after a small delay
                            setTimeout(() => {
                                mainImage.src = element.dataset.src;
                                
                                // Remove active class from all thumbnails
                                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                                
                                // Add active class to clicked thumbnail
                                element.classList.add('active');
                                
                                // Fade in new image
                                mainImage.style.opacity = '1';
                            }, 200);
                        }

                        // Add hover effect to thumbnails
                        document.querySelectorAll('.thumbnail-image').forEach(thumb => {
                            thumb.addEventListener('mouseenter', function() {
                                if (!this.classList.contains('active')) {
                                    this.style.transform = 'scale(1.1)';
                                }
                            });
                            
                            thumb.addEventListener('mouseleave', function() {
                                if (!this.classList.contains('active')) {
                                    this.style.transform = 'scale(1)';
                                }
                            });
                        });
                        </script>
                    </div>
                    <div class="product-details">
                        <h4>
                            <?= htmlspecialchars($product['name']) ?>
                            <?php if($product['trending']) { ?>
                                <span class="float-end badge bg-danger">Trending</span>
                            <?php } ?>
                        </h4>
                        <hr>
                        <div class="price-info">
                            <h5>Giá: <span class="selling-price"><?= number_format($product['selling_price'],0,",",".") ?> VND</span></h5>
                            <h5>Giá gốc: <s class="original-price"><?= number_format($product['original_price'],0,",",".") ?> VND</s></h5>
                        </div>

                        <?php if($product['qty'] > 0) { ?>
                            <div class="product_data">
                                <input type="hidden" class="prod_id" value="<?= $product['id'] ?>">
                                <input type="hidden" class="prod_name" value="<?= htmlspecialchars($product['name']) ?>">
                                <input type="hidden" class="prod_price" value="<?= $product['selling_price'] ?>">
                                <input type="hidden" class="prod_image" value="<?= htmlspecialchars($product['image']) ?>">

                                <div class="input-group mb-3">
                                    <button class="input-group-text decrement-btn">-</button>
                                    <input type="text" class="form-control text-center input-qty bg-white" 
                                           value="1" 
                                           data-max="<?= $product['qty'] ?>"
                                           readonly>
                                    <button class="input-group-text increment-btn">+</button>
                                </div>
                                <button type="button" class="btn btn-primary addToCartBtn">
                                    <i class="fa fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                                </button>

                                <div class="stock-info mt-2">Còn <?= $product['qty'] ?> sản phẩm</div>

                                <script>
                                $(document).ready(function() {
                                    // Debug: In ra thông tin sản phẩm
                                    console.log('Product container:', $('.product_data').length);
                                    console.log('Product ID:', $('.prod_id').val());
                                    console.log('Product Name:', $('.prod_name').val());
                                    console.log('Product Price:', $('.prod_price').val());
                                    console.log('Product Image:', $('.prod_image').val());
                                    console.log('Product Quantity:', $('.input-qty').val());
                                    
                                    // Debug: Kiểm tra sự kiện click
                                    $('.addToCartBtn').on('click', function() {
                                        console.log('Button clicked');
                                        var $productData = $(this).closest('.product_data');
                                        console.log('Found product_data:', $productData.length);
                                        console.log('Product data values:', {
                                            id: $productData.find('.prod_id').val(),
                                            name: $productData.find('.prod_name').val(),
                                            price: $productData.find('.prod_price').val(),
                                            image: $productData.find('.prod_image').val(),
                                            qty: $productData.find('.input-qty').val()
                                        });
                                    });
                                });
                                </script>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>Sản phẩm đã hết hàng
                            </div>
                        <?php } ?>

                        <div class="product-description">
                            <h6 class="fw-bold">Mô tả sản phẩm:</h6>
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-section">
                <div class="container reviews-section">
                    <div class="reviews-header">
                        <h3>Đánh giá sản phẩm</h3>
                        <?php 
                        // Debug: Uncomment to enable debug output
                        /*
                        if (isset($_SESSION['auth'])) {
                            echo "Debug: User ID = " . htmlspecialchars($_SESSION['auth_user']['id']) . "<br>";
                            echo "Debug: Product ID = " . htmlspecialchars($product['id']) . "<br>";
                            echo "Debug: hasUserPurchased = " . (hasUserPurchased($product['id'], $_SESSION['auth_user']['id']) ? 'true' : 'false') . "<br>";
                        }
                        */
                        if(isset($_SESSION['auth']) && hasUserPurchased($product['id'], $_SESSION['auth_user']['id'])): ?>
                            <button class="btn btn-primary">
                                <i class="fas fa-pen me-2"></i>Viết Đánh giá
                            </button>
                        <?php else: ?>
                            <div class="alert alert-warning mt-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>Bạn cần mua hàng và hoàn thành đơn hàng trước khi đánh giá sản phẩm
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php 
                    $stats = getProductReviewStats($product['id']);
                    $total_reviews = $stats['total_reviews'];
                    $avg_rating = number_format($stats['average_rating'], 1);
                    ?>

                    <div class="rating-summary">
                        <div class="average-rating">
                            <div class="rating-circle" style="--rating-percent: <?= $avg_rating * 20 ?>">
                                <div class="rating-number"><?= $avg_rating ?></div>
                            </div>
                            <div class="rating-details">
                                <div class="rating-stars">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= round($avg_rating) ? 'active' : '' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <div class="rating-count"><?= $total_reviews ?> Đánh giá</div>
                            </div>
                        </div>
                        <div class="rating-bars">
                            <?php 
                            $star_keys = [
                                5 => 'five_star',
                                4 => 'four_star',
                                3 => 'three_star',
                                2 => 'two_star',
                                1 => 'one_star'
                            ];
                            for($i = 5; $i >= 1; $i--): 
                                $count = $stats[$star_keys[$i]] ?? 0;
                                $percent = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
                            ?>
                            <div class="rating-bar-item">
                                <span class="rating-bar-label"><?= $i ?> sao</span>
                                <div class="rating-bar-bg">
                                    <div class="rating-bar-fill" style="width: <?= $percent ?>%"></div>
                                </div>
                                <span class="rating-bar-count"><?= $count ?></span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="review-filters">
                        <button class="filter-button active" data-filter="all">Tất cả</button>
                        <button class="filter-button" data-filter="5">5 sao</button>
                        <button class="filter-button" data-filter="4">4 sao</button>
                        <button class="filter-button" data-filter="3">3 sao</button>
                        <button class="filter-button" data-filter="2">2 sao</button>
                        <button class="filter-button" data-filter="1">1 sao</button>
                        <button class="filter-button" data-filter="images">Có hình ảnh</button>
                        <button class="filter-button" data-filter="verified">Đã mua hàng</button>
                    </div>

                    <div class="review-list">
                        <?php
                        $reviews = getProductReviews($product['id']);
                        if($reviews && count($reviews) > 0):
                            foreach($reviews as $review):
                        ?>
                        <div class="review-item" 
                             data-rating="<?= $review['rating'] ?>"
                             data-has-images="<?= !empty($review['images']) ? '1' : '0' ?>"
                             data-verified="<?= $review['has_purchased'] ? '1' : '0' ?>">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">
                                    <?php if($review['reviewer_avatar']): ?>
                                        <img src="./uploads/users/<?= htmlspecialchars($review['reviewer_avatar']) ?>" alt="Avatar">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($review['reviewer_name'], 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h5 class="reviewer-name">
                                        <?= htmlspecialchars($review['reviewer_name']) ?>
                                        <?php if($review['has_purchased']): ?>
                                        <span class="verified-badge">
                                            <i class="fas fa-check-circle"></i>Đã mua hàng
                                        </span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="review-date"><?= date('d/m/Y', strtotime($review['created_at'])) ?></p>
                                </div>
                            </div>
                            <div class="review-rating">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $review['rating'] ? 'active' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="review-content">
                                <?= nl2br(htmlspecialchars($review['comment'])) ?>
                            </div>
                            <?php if(!empty($review['images'])): ?>
                            <div class="review-images">
                                <?php 
                                $images = is_array($review['images']) ? $review['images'] : explode(',', $review['images']);
                                foreach($images as $image): 
                                    if(!empty($image)):
                                ?>
                                <img src="uploads/reviews/<?= htmlspecialchars($image) ?>" 
                                     alt="Review Image" 
                                     class="review-image"
                                     onclick="showImagePreview(this.src)">
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <?php endif; ?>
                            <div class="review-actions">
                                <button class="helpful-btn" onclick="markHelpful(<?= $review['id'] ?>)">
                                    <i class="far fa-thumbs-up"></i>Hữu ích
                                </button>
                                <span class="helpful-count"><?= $review['helpful_count'] ?? 0 ?> người thấy hữu ích</span>
                            </div>
                            <?php if(!empty($review['shop_reply'])): ?>
                            <div class="review-reply">
                                <span class="shop-reply-badge">
                                    <i class="fas fa-store"></i>Phản hồi từ shop
                                </span>
                                <p><?= nl2br(htmlspecialchars($review['shop_reply'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comment-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <section class="review-form-section">
                <div class="container review-form">
                    <?php if(isset($_SESSION['auth']) && hasUserPurchased($product['id'], $_SESSION['auth_user']['id'])): ?>
                        <h3>Viết Đánh giá của bạn</h3>
                        <form action="functions/handle-review.php" method="POST" enctype="multipart/form-data" id="reviewForm">
                            <input type="hidden" name="action" value="submit_review">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                            <div class="form-group">
                                <label class="form-label">Đánh giá của bạn <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    <?php for($i = 5; $i >= 1; $i--): ?>
                                        <i class="fas fa-star rating-star" data-value="<?= $i ?>"></i>
                                    <?php endfor; ?>
                                    <span class="rating-text"></span>
                                </div>
                                <input type="hidden" name="rating" id="rating-value" value="0">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nhận xét của bạn <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="comment" rows="5" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                            </div>

                            <div class="form-group image-upload-container">
                                <label class="form-label">Thêm hình ảnh (tối đa 5 ảnh)</label>
                                <div class="image-upload-preview">
                                    <div class="upload-box" id="uploadBox">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                                <input type="file" name="images[]" id="imageInput" multiple accept="image/*" style="display: none;">
                                <p class="upload-instructions">Hỗ trợ: JPG, JPEG, PNG (tối đa 5MB/ảnh)</p>
                                <p class="image-upload-error" id="imageError"></p>
                            </div>

                            <button type="submit" class="submit-btn">Gửi Đánh giá</button>
                        </form>
                        
                        <script>
                        document.getElementById('reviewForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            const formData = new FormData(this);
                            
                            // Kiểm tra rating
                            const rating = formData.get('rating');
                            if (!rating || rating === '0') {
                                alert('Vui lòng chọn số sao đánh giá');
                                return;
                            }
                            
                            // Kiểm tra comment
                            const comment = formData.get('comment').trim();
                            if (!comment) {
                                alert('Vui lòng nhập nhận xét của bạn');
                                return;
                            }
                            
                            // Gửi form bằng AJAX
                            fetch('functions/handle-review.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.message || 'Cảm ơn bạn đã đánh giá sản phẩm');
                                    location.reload();
                                } else {
                                    alert(data.message || 'Có lỗi xảy ra khi gửi đánh giá');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Có lỗi xảy ra khi gửi đánh giá');
                            });
                        });
                        
                        // Xử lý upload ảnh
                        const uploadBox = document.getElementById('uploadBox');
                        const imageInput = document.getElementById('imageInput');
                        const previewContainer = document.querySelector('.image-upload-preview');
                        const errorText = document.getElementById('imageError');
                        const maxFiles = 5;
                        const maxFileSize = 5 * 1024 * 1024; // 5MB
                        
                        uploadBox.addEventListener('click', () => imageInput.click());
                        
                        function showError(message) {
                            errorText.textContent = message;
                            errorText.style.display = 'block';
                            setTimeout(() => {
                                errorText.style.display = 'none';
                            }, 3000);
                        }
                        
                        function createPreviewItem(file) {
                            const reader = new FileReader();
                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';
                            
                            reader.onload = function(e) {
                                previewItem.innerHTML = `
                                    <img src="${e.target.result}" alt="Preview">
                                    <button type="button" class="remove-image">
                                        <i class="fas fa-times"></i>
                                    </button>
                                `;
                                
                                previewItem.querySelector('.remove-image').addEventListener('click', function() {
                                    previewItem.remove();
                                    updateUploadBox();
                                    
                                    // Cập nhật input file
                                    const dt = new DataTransfer();
                                    const files = Array.from(imageInput.files);
                                    files.forEach(f => {
                                        if (f !== file) dt.items.add(f);
                                    });
                                    imageInput.files = dt.files;
                                });
                            };
                            
                            reader.readAsDataURL(file);
                            return previewItem;
                        }
                        
                        function updateUploadBox() {
                            const previewItems = previewContainer.querySelectorAll('.preview-item');
                            if (previewItems.length >= maxFiles) {
                                uploadBox.style.display = 'none';
                            } else {
                                uploadBox.style.display = 'flex';
                            }
                        }
                        
                        imageInput.addEventListener('change', function() {
                            const files = Array.from(this.files);
                            
                            // Kiểm tra số lượng file
                            if (files.length > maxFiles) {
                                showError(`Chỉ được chọn tối đa ${maxFiles} ảnh`);
                                this.value = '';
                                return;
                            }
                            
                            // Kiểm tra kích thước và định dạng file
                            const invalidFiles = files.filter(file => {
                                const isValidSize = file.size <= maxFileSize;
                                const isValidType = ['image/jpeg', 'image/png', 'image/jpg'].includes(file.type);
                                return !isValidSize || !isValidType;
                            });
                            
                            if (invalidFiles.length > 0) {
                                showError('Một số ảnh không hợp lệ (kích thước > 5MB hoặc định dạng không hỗ trợ)');
                                this.value = '';
                                return;
                            }
                            
                            // Xóa tất cả preview hiện tại
                            const previewItems = previewContainer.querySelectorAll('.preview-item');
                            previewItems.forEach(item => item.remove());
                            
                            // Thêm các preview mới
                            files.forEach(file => {
                                const previewItem = createPreviewItem(file);
                                previewContainer.insertBefore(previewItem, uploadBox);
                            });
                            
                            updateUploadBox();
                        });
                        </script>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>Bạn cần mua hàng và hoàn thành đơn hàng trước khi đánh giá sản phẩm
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <div id="imageModal" class="modal">
                <span class="close">×</span>
                <img class="modal-content" id="previewImage">
            </div>

            <script>
            // Image Gallery
            document.addEventListener('DOMContentLoaded', function() {
                const mainImage = document.getElementById('mainImage');
                const thumbnails = document.querySelectorAll('.thumbnail-image');

                window.changeMainImage = function(element) {
                    if (!element || !mainImage) return;
                    
                    // Start fade out
                    mainImage.style.opacity = '0';
                    
                    // Change source after a small delay
                    setTimeout(() => {
                        mainImage.src = element.dataset.src;
                        
                        // Remove active class from all thumbnails
                        thumbnails.forEach(thumb => thumb.classList.remove('active'));
                        
                        // Add active class to clicked thumbnail
                        element.classList.add('active');
                        
                        // Fade in new image
                        mainImage.style.opacity = '1';
                    }, 200);
                }

                // Add hover effect to thumbnails
                thumbnails.forEach(thumb => {
                    thumb.addEventListener('mouseenter', function() {
                        if (!this.classList.contains('active')) {
                            this.style.transform = 'scale(1.1)';
                        }
                    });
                    
                    thumb.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('active')) {
                            this.style.transform = 'scale(1)';
                        }
                    });
                });

                // Optional: Preload images for smoother transitions
                window.addEventListener('load', function() {
                    thumbnails.forEach(thumbnail => {
                        if (thumbnail.dataset.src) {
                            const img = new Image();
                            img.src = thumbnail.dataset.src;
                        }
                    });
                });

                // Set initial state
                mainImage.style.opacity = '1';
                thumbnails[0]?.classList.add('active');
            });

            // Star Rating System
            document.addEventListener('DOMContentLoaded', function() {
                const reviewForm = document.getElementById('reviewForm');
                const stars = document.querySelectorAll('.rating-star');
                const ratingInput = document.getElementById('rating-value');
                const ratingText = document.querySelector('.rating-text');

                // Texts for rating values
                const ratingTexts = {
                    1: 'Rất tệ',
                    2: 'Tệ',
                    3: 'Bình thường',
                    4: 'Tốt',
                    5: 'Rất tốt'
                };

                // Function to update stars display
                function updateStars(value, isHover = false) {
                    stars.forEach((star) => {
                        const starValue = parseInt(star.dataset.value);
                        if (starValue <= value) {
                            star.classList.add(isHover ? 'hover' : 'active');
                        } else {
                            star.classList.remove(isHover ? 'hover' : 'active');
                        }
                    });
                    
                    // Update rating text
                    ratingText.textContent = ratingTexts[value] || '';
                }

                // Handle star hover
                stars.forEach(star => {
                    star.addEventListener('mouseenter', () => {
                        const value = parseInt(star.dataset.value);
                        updateStars(value, true);
                    });

                    star.addEventListener('click', () => {
                        const value = parseInt(star.dataset.value);
                        ratingInput.value = value;
                        console.log('Rating clicked:', value);
                        console.log('Rating input value:', ratingInput.value);
                        
                        // Remove hover class from all stars
                        stars.forEach(s => s.classList.remove('hover'));
                        
                        // Add active class to selected stars
                        updateStars(value);
                    });
                });

                // Handle mouse leave from star rating container
                document.querySelector('.star-rating').addEventListener('mouseleave', () => {
                    stars.forEach(star => star.classList.remove('hover'));
                    const currentRating = parseInt(ratingInput.value) || 0;
                    if (currentRating > 0) {
                        updateStars(currentRating);
                    }
                });

                // Form submission validation
                if (reviewForm) {
                    reviewForm.addEventListener('submit', function(e) {
                        e.preventDefault(); // Prevent default submission first
                        
                        const rating = parseInt(ratingInput.value) || 0;
                        console.log('Form submitted with rating:', rating);
                        console.log('Rating input element:', ratingInput);
                        console.log('Rating input value:', ratingInput.value);
                        
                        if (rating === 0) {
                            alert('Vui lòng chọn số sao đánh giá');
                            return false;
                        }
                        
                        // If rating is valid, submit the form
                        reviewForm.submit();
                    });
                }
            });

            // Image Preview and Filters
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('imageModal');
                const modalImg = document.getElementById('previewImage');

                document.querySelectorAll('.review-image').forEach(image => {
                    image.addEventListener('click', function() {
                        modal.style.display = 'flex';
                        modal.classList.add('active');
                        modalImg.src = this.src;
                    });
                });

                document.querySelector('.close').addEventListener('click', function() {
                    modal.style.display = 'none';
                    modal.classList.remove('active');
                });

                const uploadPreview = document.querySelector('.upload-preview');
                const fileInput = document.querySelector('input[type="file"]');

                uploadPreview.addEventListener('click', () => fileInput.click());

                fileInput.addEventListener('change', function() {
                    const files = Array.from(this.files);
                    const imageUpload = document.querySelector('.image-upload');
                    
                    const existingPreviews = imageUpload.querySelectorAll('.upload-preview:not(:first-child)');
                    existingPreviews.forEach(preview => preview.remove());

                    files.forEach((file, index) => {
                        if (index < 5) {
                            const reader = new FileReader();
                            const preview = document.createElement('div');
                            preview.className = 'upload-preview';
                            
                            reader.onload = e => {
                                preview.style.backgroundImage = `url(${e.target.result})`;
                                preview.style.backgroundSize = 'cover';
                                preview.style.backgroundPosition = 'center';
                            };
                            
                            reader.readAsDataURL(file);
                            imageUpload.appendChild(preview);
                        }
                    });
                });

                document.querySelectorAll('.filter-button').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelector('.filter-button.active').classList.remove('active');
                        this.classList.add('active');
                    });
                });

                document.querySelectorAll('.helpful-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        this.classList.toggle('active');
                    });
                });
            });
            </script>
<?php
        } else {
            echo '<div class="container mt-3"><div class="alert alert-danger">Dữ liệu sản phẩm không hợp lệ</div></div>';
        }
    } else {
        echo '<div class="container mt-3"><div class="alert alert-danger">Không tìm thấy sản phẩm</div></div>';
    }
} else {
    echo '<div class="container mt-3"><div class="alert alert-danger">Vui lòng cung cấp slug sản phẩm</div></div>';
}

include("./includes/footer.php");
?>
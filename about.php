<?php 
include('functions/userfunctions.php');
include('includes/header.php');
?>

<div class="about-page">
    <div class="about-header">
        <div class="container">
            <h1>Về Chúng Tôi</h1>
            <p>Chào mừng đến với Q-Fashion - Nơi Phong Cách Gặp Gỡ Chất Lượng</p>
        </div>
    </div>

    <div class="about-content">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="about-text">
                        <h2>Câu Chuyện Của Chúng Tôi</h2>
                        <p>Q-Fashion được thành lập với sứ mệnh mang đến những sản phẩm thời trang chất lượng cao với giá cả hợp lý cho người tiêu dùng Việt Nam.</p>
                        <div class="about-features">
                            <div class="feature">
                                <i class="fas fa-check-circle"></i>
                                <span>Sản phẩm chất lượng cao</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-truck"></i>
                                <span>Giao hàng toàn quốc</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-headset"></i>
                                <span>Hỗ trợ 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                                          <div class="about-image">
                         <img src="anh_xedap/cuahangxedap.jpg" alt="Q-Fashion Store">
                      </div>
                </div>
            </div>

            <div class="about-values">
                <h2>Giá Trị Cốt Lõi</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="value-card">
                            <i class="fas fa-heart"></i>
                            <h3>Chất Lượng</h3>
                            <p>Cam kết mang đến những sản phẩm chất lượng tốt nhất cho khách hàng</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="value-card">
                            <i class="fas fa-star"></i>
                            <h3>Sáng Tạo</h3>
                            <p>Không ngừng đổi mới và cập nhật xu hướng thời trang mới nhất</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="value-card">
                            <i class="fas fa-handshake"></i>
                            <h3>Tận Tâm</h3>
                            <p>Luôn đặt sự hài lòng của khách hàng lên hàng đầu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-page {
    padding-bottom: 60px;
}

.about-header {
    background: linear-gradient(90deg, #3d5af1 0%, #0b1e6b 100%);
    color: #fff;
    padding: 60px 0;
    text-align: center;
    margin-bottom: 60px;
}

.about-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.about-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.about-text {
    padding-right: 30px;
}

.about-text h2 {
    color: #0b1e6b;
    font-size: 2rem;
    margin-bottom: 20px;
    font-weight: 600;
}

.about-text p {
    color: #555;
    line-height: 1.8;
    margin-bottom: 30px;
}

.about-features {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.feature {
    display: flex;
    align-items: center;
    gap: 10px;
}

.feature i {
    color: #3d5af1;
    font-size: 20px;
}

.feature span {
    color: #333;
    font-weight: 500;
}

.about-image {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.about-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.about-values {
    margin-top: 80px;
    text-align: center;
}

.about-values h2 {
    color: #0b1e6b;
    font-size: 2rem;
    margin-bottom: 50px;
    font-weight: 600;
}

.value-card {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.value-card:hover {
    transform: translateY(-5px);
}

.value-card i {
    font-size: 40px;
    color: #3d5af1;
    margin-bottom: 20px;
}

.value-card h3 {
    color: #0b1e6b;
    font-size: 1.3rem;
    margin-bottom: 15px;
    font-weight: 600;
}

.value-card p {
    color: #666;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .about-text {
        padding-right: 0;
        margin-bottom: 40px;
        text-align: center;
    }

    .about-features {
        align-items: center;
    }

    .value-card {
        margin-bottom: 30px;
    }
}
</style>

<?php include('includes/footer.php'); ?> 
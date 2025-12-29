<?php 
include('functions/userfunctions.php');
include('includes/header.php');
?>

<div class="contact-page">
    <div class="contact-header">
        <div class="container">
            <h1>Liên Hệ</h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe ý kiến của bạn</p>
        </div>
    </div>

    <div class="contact-content">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="contact-info">
                        <h2>Thông Tin Liên Hệ</h2>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h3>Địa Chỉ</h3>
                                <p>123 Đường ABC, Quận XYZ, TP.HCM</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone-alt"></i>
                            <div>
                                <h3>Điện Thoại</h3>
                                <p>Hotline: 1800 9473</p>
                                <p>Bảo hành: 1800 9063</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h3>Email</h3>
                                <p>support@qfashion.com</p>
                                <p>sales@qfashion.com</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h3>Giờ Làm Việc</h3>
                                <p>Thứ 2 - Thứ 7: 8:00 - 21:00</p>
                                <p>Chủ nhật: 8:00 - 18:00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-form">
                        <h2>Gửi Tin Nhắn</h2>
                        <form id="contactForm">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Họ và tên" required>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <input type="tel" class="form-control" placeholder="Số điện thoại">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" rows="5" placeholder="Nội dung tin nhắn" required></textarea>
                            </div>
                            <button type="submit" class="btn-submit">Gửi Tin Nhắn</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4241674197956!2d106.65842911471821!3d10.773374992323565!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ec3c161a3fb%3A0xef77cd47a1cc691e!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBCw6FjaCBraG9hIFRQLkhDTQ!5e0!3m2!1svi!2s!4v1645246805419!5m2!1svi!2s" 
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

<style>
.contact-page {
    padding-bottom: 60px;
}

.contact-header {
    background: linear-gradient(90deg, #3d5af1 0%, #0b1e6b 100%);
    color: #fff;
    padding: 60px 0;
    text-align: center;
    margin-bottom: 60px;
}

.contact-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.contact-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.contact-content {
    margin-bottom: 60px;
}

.contact-info h2,
.contact-form h2 {
    color: #0b1e6b;
    font-size: 1.8rem;
    margin-bottom: 30px;
    font-weight: 600;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 30px;
}

.info-item i {
    font-size: 24px;
    color: #3d5af1;
    margin-top: 5px;
}

.info-item h3 {
    color: #333;
    font-size: 1.1rem;
    margin-bottom: 5px;
    font-weight: 600;
}

.info-item p {
    color: #666;
    margin-bottom: 5px;
    line-height: 1.6;
}

.contact-form {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 20px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #3d5af1;
    box-shadow: 0 0 0 3px rgba(61, 90, 241, 0.1);
    outline: none;
}

textarea.form-control {
    resize: none;
}

.btn-submit {
    background: #3d5af1;
    color: #fff;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-submit:hover {
    background: #0b1e6b;
    transform: translateY(-2px);
}

.contact-map {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
    .contact-info {
        margin-bottom: 40px;
    }

    .contact-form {
        margin-bottom: 40px;
    }
}
</style>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Cảm ơn bạn đã liên hệ với chúng tôi. Chúng tôi sẽ phản hồi sớm nhất có thể!');
    this.reset();
});
</script>

<?php include('includes/footer.php'); ?> 
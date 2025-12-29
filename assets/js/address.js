// Dữ liệu quận/huyện theo tỉnh/thành phố
const districtData = {
    'An Giang': ['Long Xuyên', 'Châu Đốc', 'Tân Châu', 'An Phú', 'Châu Phú', 'Châu Thành', 'Chợ Mới', 'Phú Tân', 'Thoại Sơn', 'Tịnh Biên', 'Tri Tôn'],
    'Bà Rịa - Vũng Tàu': ['Vũng Tàu', 'Bà Rịa', 'Châu Đức', 'Côn Đảo', 'Đất Đỏ', 'Long Điền', 'Tân Thành', 'Xuyên Mộc'],
    'Bắc Giang': ['Bắc Giang', 'Hiệp Hòa', 'Lạng Giang', 'Lục Nam', 'Lục Ngạn', 'Sơn Động', 'Tân Yên', 'Việt Yên', 'Yên Dũng', 'Yên Thế'],
    'Bắc Kạn': ['Bắc Kạn', 'Ba Bể', 'Bạch Thông', 'Chợ Đồn', 'Chợ Mới', 'Na Rì', 'Ngân Sơn', 'Pác Nặm'],
    'Bạc Liêu': ['Bạc Liêu', 'Đông Hải', 'Giá Rai', 'Hòa Bình', 'Hồng Dân', 'Phước Long', 'Vĩnh Lợi'],
    'Bắc Ninh': ['Bắc Ninh', 'Gia Bình', 'Lương Tài', 'Quế Võ', 'Thuận Thành', 'Tiên Du', 'Từ Sơn', 'Yên Phong'],
    'Bến Tre': ['Bến Tre', 'Ba Tri', 'Bình Đại', 'Châu Thành', 'Chợ Lách', 'Giồng Trôm', 'Mỏ Cày Bắc', 'Mỏ Cày Nam', 'Thạnh Phú'],
    'Hà Nội': ['Ba Đình', 'Hoàn Kiếm', 'Hai Bà Trưng', 'Đống Đa', 'Tây Hồ', 'Cầu Giấy', 'Thanh Xuân', 'Hoàng Mai', 'Long Biên', 'Nam Từ Liêm', 'Bắc Từ Liêm', 'Hà Đông', 'Sơn Tây', 'Ba Vì', 'Chương Mỹ', 'Đan Phượng', 'Đông Anh', 'Gia Lâm', 'Hoài Đức', 'Mê Linh', 'Mỹ Đức', 'Phú Xuyên', 'Phúc Thọ', 'Quốc Oai', 'Sóc Sơn', 'Thạch Thất', 'Thanh Oai', 'Thanh Trì', 'Thường Tín', 'Ứng Hòa'],
    'TP Hồ Chí Minh': ['Quận 1', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 6', 'Quận 7', 'Quận 8', 'Quận 10', 'Quận 11', 'Quận 12', 'Thủ Đức', 'Gò Vấp', 'Bình Thạnh', 'Tân Bình', 'Tân Phú', 'Phú Nhuận', 'Bình Tân', 'Củ Chi', 'Hóc Môn', 'Bình Chánh', 'Nhà Bè', 'Cần Giờ'],
    'Đà Nẵng': ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn', 'Liên Chiểu', 'Cẩm Lệ', 'Hòa Vang', 'Hoàng Sa'],
    // Thêm dữ liệu cho các tỉnh/thành phố khác
};

// Hàm cập nhật danh sách quận/huyện
function updateDistricts(province, selectedDistrict = '') {
    const districtSelect = document.getElementById('district');
    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
    
    if (province && districtData[province]) {
        districtData[province].forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            if (district === selectedDistrict) {
                option.selected = true;
            }
            districtSelect.appendChild(option);
        });
    }
}

// Hàm khởi tạo
function initializeAddress() {
    const provinceSelect = document.getElementById('province');
    const addressParts = document.getElementById('current_address').value.split(', ');
    
    if (addressParts.length >= 2) {
        const province = addressParts[addressParts.length - 1];
        const district = addressParts[addressParts.length - 2];
        
        if (province) {
            provinceSelect.value = province;
            updateDistricts(province, district);
        }
    }
}

// Hàm cập nhật địa chỉ đầy đủ
function updateFullAddress() {
    const specificAddress = document.getElementById('specific_address').value;
    const district = document.getElementById('district').value;
    const province = document.getElementById('province').value;
    
    if (specificAddress && district && province) {
        const fullAddress = `${specificAddress}, ${district}, ${province}`;
        document.getElementById('full_address').value = fullAddress;
        return true;
    }
    return false;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const addressForm = document.querySelector('form');
    
    // Khởi tạo địa chỉ khi trang load
    initializeAddress();
    
    // Cập nhật quận/huyện khi thay đổi tỉnh/thành phố
    provinceSelect.addEventListener('change', function() {
        updateDistricts(this.value);
    });
    
    // Cập nhật địa chỉ đầy đủ khi submit form
    addressForm.addEventListener('submit', function(e) {
        if (!updateFullAddress()) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin địa chỉ');
        }
    });
}); 
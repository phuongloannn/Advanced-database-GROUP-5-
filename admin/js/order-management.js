// Hàm cập nhật trạng thái đơn hàng
function updateOrderStatus(orderId, status) {
    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('order_status', status);
    formData.append('update_order_btn', true);

    fetch('../functions/handle-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 200) {
            // Hiển thị thông báo thành công
            showNotification(data.message, 'success');
            
            // Cập nhật UI
            updateOrderUI(orderId, status);
            
            // Cập nhật biểu đồ thống kê (nếu đang ở trang dashboard)
            if (typeof updateDashboardCharts === 'function') {
                updateDashboardCharts();
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật trạng thái', 'error');
    });
}

// Hàm hiển thị thông báo
function showNotification(message, type) {
    // Tạo element thông báo
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} notification`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        padding: 15px 25px;
        border-radius: 4px;
        animation: slideIn 0.5s ease-out;
    `;
    notification.textContent = message;

    // Thêm vào body
    document.body.appendChild(notification);

    // Xóa thông báo sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.5s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}

// Hàm cập nhật giao diện sau khi thay đổi trạng thái
function updateOrderUI(orderId, newStatus) {
    // Cập nhật badge trạng thái
    const statusBadge = document.querySelector(`#order-${orderId} .status-badge`);
    if (statusBadge) {
        statusBadge.textContent = newStatus;
        // Cập nhật class của badge
        statusBadge.className = 'badge status-badge ' + getStatusClass(newStatus);
    }

    // Cập nhật select box (nếu có)
    const statusSelect = document.querySelector(`#status-select-${orderId}`);
    if (statusSelect) {
        statusSelect.value = newStatus;
    }
}

// Hàm lấy class tương ứng với trạng thái
function getStatusClass(status) {
    switch(status) {
        case 'Pending':
            return 'bg-warning';
        case 'Processing':
            return 'bg-info';
        case 'Completed':
            return 'bg-success';
        case 'Cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// CSS cho animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style); 
document.addEventListener('DOMContentLoaded', function() {
    const menuTrigger = document.querySelector('.user-menu-trigger');
    const menu = document.querySelector('.user-menu');
    
    // Toggle menu khi click vào avatar
    menuTrigger?.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.toggle('active');
    });
    
    // Đóng menu khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!menu?.contains(e.target) && !menuTrigger?.contains(e.target)) {
            menu?.classList.remove('active');
        }
    });
    
    // Đóng menu khi nhấn ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            menu?.classList.remove('active');
        }
    });
    
    // Xử lý đăng xuất
    const logoutBtn = document.querySelector('.logout-item');
    logoutBtn?.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
            window.location.href = this.href;
        }
    });
}); 
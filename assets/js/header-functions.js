$(document).ready(function() {
    // Sticky Header
    let header = $('.header');
    let headerOffset = header.offset().top;

    $(window).scroll(function() {
        if ($(window).scrollTop() > headerOffset) {
            header.addClass('sticky');
        } else {
            header.removeClass('sticky');
        }
    });

    // Search Functionality
    const searchForm = $('.search-form');
    const searchInput = $('.search-input');

    searchForm.on('submit', function(e) {
        const searchTerm = searchInput.val().trim();
        if (searchTerm === '') {
            e.preventDefault();
            showAlert('Vui lòng nhập từ khóa tìm kiếm', 'warning');
        }
    });

    // Cart Functions
    $('.add-to-cart-btn').on('click', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const quantity = $(this).closest('.product-item').find('.quantity-input').val() || 1;

        $.ajax({
            url: 'functions/handlecart.php',
            type: 'POST',
            data: {
                'prod_id': productId,
                'prod_qty': quantity,
                'scope': 'add'
            },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 200) {
                    updateCartCount();
                    showAlert(res.message, 'success');
                } else {
                    showAlert(res.message, 'error');
                }
            }
        });
    });

    // Update Cart Count
    function updateCartCount() {
        $.ajax({
            url: 'functions/cart-count.php',
            type: 'GET',
            success: function(response) {
                const count = parseInt(response);
                if (count > 0) {
                    $('.cart-count').text(count).show();
                } else {
                    $('.cart-count').hide();
                }
            }
        });
    }

    // Wishlist Functions
    $('.add-to-wishlist').on('click', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');

        $.ajax({
            url: 'functions/wishlist-handler.php',
            type: 'POST',
            data: {
                'prod_id': productId,
                'action': 'add'
            },
            success: function(response) {
                const res = JSON.parse(response);
                showAlert(res.message, res.status === 200 ? 'success' : 'error');
                if (res.status === 200) {
                    $(e.target).addClass('active');
                }
            }
        });
    });

    // User Authentication Check
    function checkAuthStatus() {
        const protectedLinks = [
            'cart.php',
            'wishlist.php',
            'my-orders.php'
        ];

        $(document).on('click', 'a', function(e) {
            const href = $(this).attr('href');
            if (protectedLinks.some(link => href?.includes(link))) {
                if (!isUserLoggedIn()) {
                    e.preventDefault();
                    showLoginPrompt();
                }
            }
        });
    }

    function isUserLoggedIn() {
        return document.body.classList.contains('user-logged-in');
    }

    function showLoginPrompt() {
        Swal.fire({
            title: 'Đăng nhập',
            text: 'Vui lòng đăng nhập để tiếp tục',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Đăng nhập',
            cancelButtonText: 'Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php';
            }
        });
    }

    // Alert Function
    function showAlert(message, type = 'info') {
        const alertConfig = {
            success: {
                icon: 'success',
                title: 'Thành công'
            },
            error: {
                icon: 'error',
                title: 'Lỗi'
            },
            warning: {
                icon: 'warning',
                title: 'Cảnh báo'
            },
            info: {
                icon: 'info',
                title: 'Thông báo'
            }
        };

        const config = alertConfig[type];
        Swal.fire({
            icon: config.icon,
            title: config.title,
            text: message,
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }

    // Initialize Functions
    checkAuthStatus();
    updateCartCount();

    // Responsive Menu Toggle
    const menuToggle = $('<button>', {
        class: 'menu-toggle',
        html: '<i class="fas fa-bars"></i>'
    }).appendTo('.nav-menu .container');

    const navLinks = $('.nav-links');

    menuToggle.on('click', function() {
        navLinks.toggleClass('show');
        $(this).find('i').toggleClass('fa-bars fa-times');
    });

    // Close menu on window resize
    $(window).on('resize', function() {
        if (window.innerWidth > 768) {
            navLinks.removeClass('show');
            menuToggle.find('i').removeClass('fa-times').addClass('fa-bars');
        }
    });
});
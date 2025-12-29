document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail-image');

    function changeMainImage(element) {
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

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            changeMainImage(this);
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
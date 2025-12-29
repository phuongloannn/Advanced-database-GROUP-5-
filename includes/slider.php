<?php
// Slider configuration
$sliderConfig = [
    'autoplay' => true,
    'delay' => 5000,
    'transition' => 'fade'
];
?>

<!-- Slider Container -->
<div class="slider-container">
    <div class="slider-wrapper">
        <!-- Slider content will be dynamically populated by JavaScript -->
    </div>
    <div class="slider-controls">
        <button class="slider-prev"><i class='bx bx-chevron-left'></i></button>
        <button class="slider-next"><i class='bx bx-chevron-right'></i></button>
    </div>
    <div class="slider-pagination"></div>
</div>

<style>
.slider-container {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    margin-bottom: 30px;
}

.slider-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
}

.slider-controls {
    position: absolute;
    top: 50%;
    width: 100%;
    transform: translateY(-50%);
    z-index: 10;
    display: flex;
    justify-content: space-between;
    padding: 0 20px;
}

.slider-prev,
.slider-next {
    background: rgba(255, 255, 255, 0.8);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.slider-prev:hover,
.slider-next:hover {
    background: rgba(255, 255, 255, 1);
}

.slider-pagination {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
}

.slider-pagination .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider-pagination .dot.active {
    background: #fff;
}

@media (max-width: 768px) {
    .slider-container {
        height: 300px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Slider functionality will be initialized by main.js or index.js
    console.log('Slider component loaded');
});
</script> 
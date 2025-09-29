import './bootstrap';
import { initTapVibration, playVibrationPattern } from './tap-sound';

// Initialize tap vibration
document.addEventListener('DOMContentLoaded', () => {
    initTapVibration();
    // lazyLoadProductImages();
});

// For Livewire - reinitialize on page updates
document.addEventListener('livewire:navigated', () => {
    initTapVibration();
    // lazyLoadProductImages();
});

// For Livewire - re-run image lazy load after any DOM update

// Listen for custom event dispatched from Livewire (product-image-refresh)
// document.addEventListener('product-image-refresh', () => {
    // lazyLoadProductImages();
    // alert('Product images refreshed');
// });

// document.addEventListener('livewire:init', () => {
//     Livewire.hook('morph.updated', ({ el, component }) => {
//         // Your logic here, e.g.:
//         // lazyLoadProductImages();
//     });
// });

// Make vibration functions globally available for inline usage
window.playVibrationPattern = playVibrationPattern;

// Product image lazy-load and thumb-to-original swap
// function lazyLoadProductImages() {
//     const imgs = document.querySelectorAll('img.lazyload[data-src]:not([data-loaded="1"])');
//     imgs.forEach(img => {
//         // Prevent re-processing
        
//         if (img.dataset.loaded === '1') return;
//         const highResImg = new window.Image();
//         highResImg.src = img.dataset.src;
//         console.log('Lazy loading image:', img.dataset.src);
//         //check img.dataset.src has  
//         highResImg.onload = function () {
//             img.src = img.dataset.src;
//             img.classList.remove('blur-xs');
//             img.dataset.loaded = '1';
//         };
//         highResImg.onerror = function () {
//             img.src = img.dataset.src;
//             img.classList.remove('blur-xs');
//             img.dataset.loaded = '1';
//         };
//     });
// }

import './bootstrap';
import { initTapSound } from './tap-sound';

// Initialize tap sound
document.addEventListener('DOMContentLoaded', () => {
    initTapSound();
    const tapSound = new Audio('/sounds/tap.wav');
    tapSound.preload = 'auto';
});

// For Livewire - reinitialize on page updates
document.addEventListener('livewire:navigated', () => {
    initTapSound();
});
import './bootstrap';
import { initTapVibration, playVibrationPattern } from './tap-sound';

// Initialize tap vibration
document.addEventListener('DOMContentLoaded', () => {
    initTapVibration();
});

// For Livewire - reinitialize on page updates
document.addEventListener('livewire:navigated', () => {
    initTapVibration();
});

// Make vibration functions globally available for inline usage
window.playVibrationPattern = playVibrationPattern;
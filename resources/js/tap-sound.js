// Tap vibration utility
export const playTapVibration = () => {
    // Check if the device supports vibration
    if ('vibrate' in navigator) {
        // Vibrate for 50ms (short tap feedback)
        navigator.vibrate(50);
    } else {
        // Fallback for devices that don't support vibration
        console.log('Vibration not supported on this device');
    }
};

// Different vibration patterns for different interactions
export const vibrationPatterns = {
    tap: [50],           // Single short vibration
    success: [100, 50, 100], // Double vibration for success
    error: [200, 100, 200, 100, 200], // Longer pattern for errors
    warning: [150, 100, 150] // Medium pattern for warnings
};

export const playVibrationPattern = (pattern = 'tap') => {
    if ('vibrate' in navigator) {
        const vibrationPattern = vibrationPatterns[pattern] || vibrationPatterns.tap;
        navigator.vibrate(vibrationPattern);
    }
};

// Initialize tap vibration on all clickable elements
export const initTapVibration = () => {
    document.addEventListener('click', (e) => {
        // Check if the clicked element is a button, link, or has a click handler
        if (e.target.tagName === 'BUTTON' || 
            e.target.tagName == 'A' || 
            e.target.closest('button') || 
            e.target.closest('a') ||
            e.target.hasAttribute('wire:navigate') ||
            e.target.hasAttribute('wire:click') ||
            e.target.hasAttribute('@click')) {
            
            // Check for custom vibration pattern
            const vibrationPattern = e.target.getAttribute('data-vibration') || 
                                   e.target.closest('[data-vibration]')?.getAttribute('data-vibration') || 
                                   'tap';
            
            playVibrationPattern(vibrationPattern);
        }
    });
};

// Keep legacy exports for backward compatibility
export const playTapSound = playTapVibration;
export const initTapSound = initTapVibration;
// Tap sound utility
export const playTapSound = () => {
    const tapSound = new Audio('/sounds/tap.wav');
    tapSound.volume = 0.5; // Adjust volume as needed
    tapSound.play().catch(error => {
        // Silent catch - browsers may block autoplay until user interaction
        console.log('Sound play prevented by browser policy');
    });
};

// Initialize tap sound on all clickable elements
export const initTapSound = () => {
    document.addEventListener('click', (e) => {
        // Check if the clicked element is a button, link, or has a click handler
        if (e.target.tagName === 'BUTTON' || 
            e.target.tagName == 'A' || 
            e.target.closest('button') || 
            e.target.closest('a') ||
            e.target.hasAttribute('wire:navigate') ||
            e.target.hasAttribute('wire:click')) {
            playTapSound();
        }
    });
};
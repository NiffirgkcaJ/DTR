// Adjusts skeleton height dynamically
function adjustSkeletonHeight() {
    const datetimeElement = document.getElementById('datetime');
    const placeholderBars = document.querySelectorAll('.bar');

    // Get computed styles
    const fontSize = parseFloat(window.getComputedStyle(datetimeElement).getPropertyValue('font-size'));
    const lineHeight = parseFloat(window.getComputedStyle(datetimeElement).getPropertyValue('line-height')) || fontSize * 1.2; // Default to 1.2 if 'normal'
    
    const totalHeight = lineHeight * 3; // Adjust for three lines of text

    // Apply height dynamically
    placeholderBars.forEach(bar => {
        bar.style.height = `${lineHeight}px`; // Each bar matches one line of text
    });

    // Ensure placeholder container matches total text height
    document.getElementById('datetime-placeholder').style.height = `${totalHeight}px`;
}

// Time shown in the website
function updateTime() {
    const now = new Date();
    const weekString = now.toLocaleDateString('en-US', { weekday: 'long' });
    const dateString = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: '2-digit' });
    const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });

    document.getElementById('datetime').innerText = `${weekString} \n ${dateString} \n ${timeString}`;

    // Remove skeleton loader once the real time is loaded
    document.getElementById('datetime-placeholder').style.display = "none";
    document.getElementById('datetime').classList.remove("hidden");
}

// Ensure skeleton height is adjusted on load and after updates
window.addEventListener('load', adjustSkeletonHeight);
setInterval(updateTime, 1000);
updateTime(); // Initial call to display time immediately
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('theme-toggle');
    if (toggle) {
        toggle.addEventListener('change', function() {
            document.documentElement.setAttribute('data-theme', this.checked ? 'light' : 'dark');
        });
    }
});

let heliceWrapper = null;
let speed = 1;
let paused = false;

function toggleHelice() {
    heliceWrapper = document.getElementById('helice-wrapper');
    if (heliceWrapper) {
        if (paused) {
            heliceWrapper.style.animation = `rotate ${20/speed}s linear infinite`;
            event.target.textContent = '⏸️ Pause';
        } else {
            heliceWrapper.style.animation = 'none';
            event.target.textContent = '▶️ Play';
        }
        paused = !paused;
    }
}

function resetHelice() {
    heliceWrapper = document.getElementById('helice-wrapper');
    if (heliceWrapper) {
        heliceWrapper.style.animation = `rotate ${20/speed}s linear infinite`;
        heliceWrapper.style.transform = '';
        paused = false;
    }
}

function changeHeliceSpeed(delta) {
    speed = Math.max(0.5, Math.min(3, speed + delta));
    document.getElementById('speed').textContent = speed.toFixed(1);
    if (!paused) {
        document.getElementById('helice-wrapper').style.animation = `rotate ${20/speed}s linear infinite`;
    }
}
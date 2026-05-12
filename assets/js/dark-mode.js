(function() {
    'use strict';
    var storageKey = 'biodevas-theme-mode';
    var html = document.documentElement;
    var toggles = document.querySelectorAll('.dark-mode-toggle');

    if (!html || !toggles.length) return;

    // Respect reduced motion preference
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function applyTheme(mode) {
        if (mode === 'dark') {
            html.classList.add('dark-mode');
        } else {
            html.classList.remove('dark-mode');
        }
        try { localStorage.setItem(storageKey, mode); } catch(e) {}
        toggles.forEach(function(toggle) {
            toggle.setAttribute('aria-label', mode === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
            toggle.setAttribute('aria-pressed', mode === 'dark' ? 'true' : 'false');
        });
    }

    var savedMode;
    try { savedMode = localStorage.getItem(storageKey); } catch(e) {}
    var mql = window.matchMedia('(prefers-color-scheme: dark)');
    var systemMode = mql.matches ? 'dark' : 'light';
    var initialMode = savedMode || systemMode;

    if (initialMode === 'dark') {
        html.classList.add('dark-mode');
    }

    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var isDark = html.classList.contains('dark-mode');
            applyTheme(isDark ? 'light' : 'dark');
        });
    });

    // Listen for system changes only if user hasn't made a manual choice
    function onSystemChange(e) {
        var currentSaved;
        try { currentSaved = localStorage.getItem(storageKey); } catch(e2) {}
        if (!currentSaved) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    }

    // Modern API
    if (mql.addEventListener) {
        mql.addEventListener('change', onSystemChange);
    } else if (mql.addListener) {
        // Fallback for older browsers
        mql.addListener(onSystemChange);
    }
})();

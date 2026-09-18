/**
 * Convoca Theme — Frontend interactions v2
 *
 * Scroll-to-top button, header scroll shadow, and scroll animations.
 */
(function () {
    'use strict';

    /* ── Scroll-to-top button ─────────────────── */
    var btn = document.createElement('button');
    btn.className = 'scroll-to-top';
    btn.setAttribute('aria-label', 'Volver arriba');
    btn.innerHTML = '↑';
    document.body.appendChild(btn);

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* ── Header scroll shadow ─────────────────── */
    var header = document.querySelector('.site-header');

    /* ── Scroll animations ────────────────────── */
    var animateEls = document.querySelectorAll('.animate-on-scroll');

    var observer;
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        animateEls.forEach(function (el) {
            observer.observe(el);
        });
    } else {
        // Fallback: show all immediately.
        animateEls.forEach(function (el) { el.classList.add('visible'); });
    }

    /* ── Mobile Menu Gestures (Swipe to close) ── */
    var touchStartX = 0;
    var touchEndX = 0;

    document.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    document.addEventListener('touchend', function (e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        var menuOverlay = document.querySelector('.wp-block-navigation__responsive-container.is-menu-open');
        if (!menuOverlay) return;

        // If swipe left (more than 100px), close the menu
        if (touchStartX - touchEndX > 100) {
            var closeBtn = menuOverlay.querySelector('.wp-block-navigation__responsive-container-close');
            if (closeBtn) closeBtn.click();
        }
    }

    /* ── Scroll listener (throttled) ──────────── */
    var ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                var y = window.scrollY || window.pageYOffset;

                // Show/hide scroll-to-top.
                if (y > 400) {
                    btn.classList.add('visible');
                } else {
                    btn.classList.remove('visible');
                }

                // Header shrink on scroll.
                if (header) {
                    if (y > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }

                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    /* ── Buscador de cabecera ─────────────────────
       Un solo cuadro y dos botones de lupa (franja en escritorio, fila en móvil).
       El estado va en una clase para que el editor de bloques no lo borre al
       reescribir la plantilla. Escape cierra y devuelve el foco al botón visible. */
    var buscador = document.getElementById('convoca-buscador-cabecera');

    if (buscador) {
        var toggles = document.querySelectorAll('.convoca-buscar-toggle');
        var botonCerrar = buscador.querySelector('.convoca-buscador-cabecera__cerrar');
        var campoBusqueda = buscador.querySelector('.wp-block-search__input');

        var abrirBuscador = function () {
            buscador.classList.add('convoca-buscador-cabecera--abierto');
            toggles.forEach(function (t) { t.setAttribute('aria-expanded', 'true'); });
            if (campoBusqueda) campoBusqueda.focus();
        };

        var cerrarBuscador = function () {
            buscador.classList.remove('convoca-buscador-cabecera--abierto');
            toggles.forEach(function (t) { t.setAttribute('aria-expanded', 'false'); });
        };

        var estaAbierto = function () {
            return buscador.classList.contains('convoca-buscador-cabecera--abierto');
        };

        toggles.forEach(function (t) {
            t.addEventListener('click', function (e) {
                e.preventDefault();
                if (estaAbierto()) { cerrarBuscador(); } else { abrirBuscador(); }
            });
        });

        if (botonCerrar) {
            botonCerrar.addEventListener('click', cerrarBuscador);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && estaAbierto()) {
                cerrarBuscador();
                toggles.forEach(function (t) {
                    if (t.offsetParent !== null) t.focus();
                });
            }
        });

        // Un clic fuera cierra; dentro del cuadro o en un botón de lupa, no.
        document.addEventListener('click', function (e) {
            if (!estaAbierto() || buscador.contains(e.target)) return;
            var enBoton = false;
            toggles.forEach(function (t) { if (t.contains(e.target)) enBoton = true; });
            if (!enBoton) cerrarBuscador();
        });
    }
})();

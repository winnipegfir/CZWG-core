(function () {
    'use strict';
    document.querySelectorAll('[data-academy-launch-notice]').forEach(function (notice) {
        if (notice.dataset.initialized) return;
        notice.dataset.initialized = 'true';
        var nav = notice.closest('.academy-nav-item');
        var anchor = nav && nav.querySelector('[data-academy-nav-link]');
        var token = document.querySelector('meta[name="csrf-token"]');
        if (!anchor || !token) return;
        var attempted = false;
        var observer;

        function send(action) {
            return fetch(notice.dataset.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: true,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token.content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action: action })
            }).then(function (response) {
                if (!response.ok) return { show: false };
                return response.json();
            });
        }

        function reveal() {
            var bounds = anchor.getBoundingClientRect();
            if (attempted || document.hidden || bounds.width === 0 || bounds.height === 0 ||
                bounds.top < 0 || bounds.bottom > window.innerHeight) return;
            attempted = true;
            if (observer) observer.disconnect();
            // A single claim per page, only once the navigation is visible.
            send('show').then(function (result) {
                if (result.show) notice.hidden = false;
            }).catch(function () { /* Keep the navigation usable if the request fails. */ });
        }

        function dismiss() {
            notice.hidden = true;
            send('dismiss').catch(function () {});
        }
        anchor.addEventListener('click', function () { if (!notice.hidden) dismiss(); });
        document.addEventListener('visibilitychange', reveal);
        if ('IntersectionObserver' in window) {
            observer = new IntersectionObserver(function (entries) {
                if (entries.some(function (entry) { return entry.isIntersecting && entry.intersectionRatio >= 0.95; })) reveal();
            }, { threshold: 0.95 });
            observer.observe(anchor);
        } else {
            window.addEventListener('resize', reveal, { passive: true });
            window.addEventListener('scroll', reveal, { passive: true });
            document.addEventListener('click', function () { window.setTimeout(reveal, 0); });
            reveal();
        }
    });
})();

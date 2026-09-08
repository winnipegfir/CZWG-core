<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-academy-course-shell]').forEach(function (shell) {
        var sidebar = shell.querySelector('[data-academy-course-sidebar]');
        var toggle = shell.querySelector('[data-academy-sidebar-toggle]');
        var mobileToggle = shell.querySelector('[data-academy-sidebar-mobile-toggle]');
        if (!sidebar) return;

        var storageKey = 'academyCourseSidebarCollapsed';
        if (window.innerWidth >= 992 && localStorage.getItem(storageKey) === '1') {
            shell.classList.add('academy-sidebar-collapsed');
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                shell.classList.toggle('academy-sidebar-collapsed');
                localStorage.setItem(storageKey, shell.classList.contains('academy-sidebar-collapsed') ? '1' : '0');
            });
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function () {
                shell.classList.toggle('academy-sidebar-mobile-open');
            });
        }

        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                shell.classList.remove('academy-sidebar-mobile-open');
            });
        });
    });
});
</script>

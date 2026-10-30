(function () {
    'use strict';
    document.querySelectorAll('[data-sector-schedule]').forEach(function (panel) {
        var toggle = panel.querySelector('[data-schedule-toggle]');
        var fields = panel.querySelector('[data-schedule-fields]');
        var date = panel.querySelector('[data-schedule-date]');
        var hour = panel.querySelector('[data-schedule-hour]');
        var minute = panel.querySelector('[data-schedule-minute]');
        var value = panel.querySelector('[data-schedule-value]');
        var help = panel.querySelector('[data-schedule-help]');
        var summary = panel.querySelector('[data-schedule-summary]');
        var form = panel.closest('form');
        var button = form.querySelector('[data-publish-button]');
        var previous = /^(\d{4}-\d{2}-\d{2})T(\d{2}):(\d{2})$/.exec(value.value);
        if (previous) {
            date.value = previous[1];
            hour.value = previous[2];
            minute.value = previous[3];
        } else {
            hour.value = '09';
            minute.value = '00';
        }
        function update() {
            var scheduled = toggle.checked && !toggle.disabled;
            fields.hidden = !scheduled;
            toggle.setAttribute('aria-expanded', String(scheduled));
            [date, hour, minute, value].forEach(function (input) { input.disabled = !scheduled; });
            date.required = scheduled;
            value.value = scheduled && date.value ? date.value + 'T' + hour.value + ':' + minute.value : '';
            date.setCustomValidity('');
            if (scheduled && value.value && Date.parse(value.value + ':00Z') <= Date.now()) {
                date.setCustomValidity('Choose a future date and time in UTC.');
            }
            summary.textContent = value.value ? 'Scheduled for ' + date.value + ' at ' + hour.value + ':' + minute.value + ' UTC (Zulu).' : 'Choose a publication date.';
            help.textContent = scheduled ? 'Upload now; publish automatically at the selected UTC time.' : 'Publish immediately after both packages finish uploading.';
            button.textContent = scheduled ? 'Upload and schedule' : 'Upload and publish now';
        }
        [toggle, date, hour, minute].forEach(function (input) {
            input.addEventListener('change', update);
            input.addEventListener('input', update);
        });
        // Open the browser's compact month/day calendar when supported.
        date.addEventListener('click', function () {
            if (!date.disabled && typeof date.showPicker === 'function') {
                try { date.showPicker(); } catch (e) { /* Native icon/keyboard remains available. */ }
            }
        });
        form.addEventListener('submit', function (event) {
            update();
            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        });
        update();
    });
}());

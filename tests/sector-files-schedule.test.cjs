// Run with: node tests/sector-files-schedule.test.cjs
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require('node:path').join(__dirname, '../public/js/sector-files-schedule.js'), 'utf8');
function setup({ disabled = false, checked = false, previous = '' } = {}) {
    function element() {
        return { value: '', disabled: false, checked: false, events: {},
            addEventListener(name, callback) { this.events[name] = callback; },
            setAttribute(name, value) { this[name] = value; },
            setCustomValidity(message) { this.validation = message; } };
    }
    const items = {};
    ['toggle', 'fields', 'date', 'hour', 'minute', 'value', 'help', 'summary'].forEach(key => items[key] = element());
    items.toggle.disabled = disabled;
    items.toggle.checked = checked;
    items.value.value = previous;
    const button = element();
    const form = element();
    form.querySelector = () => button;
    form.checkValidity = () => !items.date.validation && (!items.date.required || !!items.date.value);
    form.reportValidity = () => {};
    const panel = { querySelector: selector => items[selector.slice(15, -1)], closest: () => form };
    vm.runInNewContext(source, { document: { querySelectorAll: () => [panel] }, Date });
    return { ...items, button, form };
}
const ui = setup();
assert.equal(ui.fields.hidden, true);
assert.equal(ui.value.disabled, true);
assert.equal(ui.button.textContent, 'Upload and publish now');
ui.toggle.checked = true;
ui.toggle.events.change();
assert.equal(ui.fields.hidden, false);
assert.equal(ui.date.required, true);
assert.equal(ui.date.disabled, false);
assert.equal(ui.button.textContent, 'Upload and schedule');
ui.date.value = '2099-01-02';
ui.hour.value = '10';
ui.minute.value = '15';
ui.date.events.change();
assert.equal(ui.value.value, '2099-01-02T10:15');
assert.match(ui.summary.textContent, /10:15 UTC/);
ui.toggle.checked = false;
ui.toggle.events.change();
assert.equal(ui.value.value, '');
assert.equal(ui.value.disabled, true);
assert.equal(ui.date.required, false);
const restored = setup({ checked: true, previous: '2099-05-06T11:23' });
assert.equal(restored.date.value, '2099-05-06');
assert.equal(restored.hour.value, '11');
assert.equal(restored.minute.value, '23');
const unavailable = setup({ checked: true, disabled: true });
assert.equal(unavailable.fields.hidden, true);
assert.equal(unavailable.value.disabled, true);
const past = setup({ checked: true, previous: '2020-01-01T09:00' });
assert.match(past.date.validation, /future/);
let prevented = false;
past.form.events.submit({ preventDefault() { prevented = true; } });
assert.equal(prevented, true);
console.log('Scheduling UI interactions passed.');

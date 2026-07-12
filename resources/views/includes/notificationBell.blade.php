<li class="nav-item dropdown mr-1">
    <a class="nav-link dropdown-toggle" href="#" id="notifBellToggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notifications" style="position:relative;">
        <i class="fas fa-bell"></i>
        <span id="notifBadge" class="notif-badge" style="display:none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right notif-dropdown" aria-labelledby="notifBellToggle">
        <div class="notif-header">
            <span class="notif-title">Notifications</span>
            <form method="POST" action="{{ route('notifications.readall') }}" class="mb-0">
                @csrf
                <button type="submit" class="notif-markall">Mark all read</button>
            </form>
        </div>
        <div id="notifList">
            <div class="notif-empty">Loading&hellip;</div>
        </div>
        <div class="notif-footer">
            <a href="{{ route('notifications.index') }}">View all</a>
        </div>
    </div>
</li>

<script>
(function () {
    var POLL_URL = '{{ route('notifications.poll') }}';
    var OPEN_URL_TEMPLATE = '{{ route('notifications.open', ['id' => '__ID__']) }}';
    var DELETE_URL_TEMPLATE = '{{ route('notifications.destroy', ['id' => '__ID__']) }}';
    var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
    var badge = document.getElementById('notifBadge');
    var list = document.getElementById('notifList');
    var lastItems = [];

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function render(items) {
        lastItems = items;
        if (!items.length) {
            list.innerHTML = '<div class="notif-empty">No notifications yet.</div>';
            return;
        }
        list.innerHTML = items.map(function (n) {
            return '<div class="notif-item' + (n.read ? '' : ' unread') + '" data-id="' + n.id + '">' +
                '<a href="' + OPEN_URL_TEMPLATE.replace('__ID__', n.id) + '" class="notif-item-link">' +
                '<div class="notif-item-icon"><i class="fas ' + escapeHtml(n.icon) + '"></i></div>' +
                '<div class="notif-item-content">' +
                '<div class="notif-item-title">' + escapeHtml(n.title) + '</div>' +
                '<div class="notif-item-body">' + escapeHtml(n.body) + '</div>' +
                '<div class="notif-item-time">' + escapeHtml(n.created_at) + '</div>' +
                '</div></a>' +
                '<button type="button" class="notif-item-delete" title="Delete" data-id="' + n.id + '"><i class="fas fa-times"></i></button>' +
                '</div>';
        }).join('');
    }

    function poll() {
        fetch(POLL_URL, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
                render(data.items);
            })
            .catch(function () {});
    }

    list.addEventListener('click', function (e) {
        var btn = e.target.closest('.notif-item-delete');
        if (!btn) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var id = btn.getAttribute('data-id');
        var row = btn.closest('.notif-item');
        fetch(DELETE_URL_TEMPLATE.replace('__ID__', id), {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
        }).then(function () {
            if (row) {
                row.remove();
            }
            if (!list.querySelector('.notif-item')) {
                list.innerHTML = '<div class="notif-empty">No notifications yet.</div>';
            }
            poll();
        });
    });

    poll();
    setInterval(poll, 45000);
})();
</script>

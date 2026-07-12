<li class="nav-item dropdown mr-1">
    <a class="nav-link dropdown-toggle" href="#" id="notifBellToggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notifications" style="position:relative;">
        <i class="fas fa-bell"></i>
        <span id="notifBadge" class="badge badge-danger" style="display:none; position:absolute; top:2px; right:-4px; font-size:0.6rem; line-height:1; padding:0.28em 0.4em; border-radius:50%;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notifBellToggle" style="width:340px; max-width:90vw; max-height:420px; overflow-y:auto; padding:0;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:0.6rem 1rem; border-bottom:1px solid rgba(0,0,0,0.08);">
            <strong style="font-size:0.85rem;">Notifications</strong>
            <form method="POST" action="{{ route('notifications.readall') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-link btn-sm p-0" style="font-size:0.72rem;">Mark all read</button>
            </form>
        </div>
        <div id="notifList">
            <div style="padding:1rem; text-align:center; color:#94a3b8; font-size:0.8rem;">Loading&hellip;</div>
        </div>
        <div style="padding:0.5rem 1rem; text-align:center; border-top:1px solid rgba(0,0,0,0.08);">
            <a href="{{ route('notifications.index') }}" style="font-size:0.75rem;">View all</a>
        </div>
    </div>
</li>

<script>
(function () {
    var POLL_URL = '{{ route('notifications.poll') }}';
    var OPEN_URL_TEMPLATE = '{{ route('notifications.open', ['id' => '__ID__']) }}';
    var badge = document.getElementById('notifBadge');
    var list = document.getElementById('notifList');

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function render(items) {
        if (!items.length) {
            list.innerHTML = '<div style="padding:1rem; text-align:center; color:#94a3b8; font-size:0.8rem;">No notifications yet.</div>';
            return;
        }
        list.innerHTML = items.map(function (n) {
            var unreadStyle = n.read ? '' : 'background:rgba(37,99,235,0.07);';
            return '<a href="' + OPEN_URL_TEMPLATE.replace('__ID__', n.id) + '" class="dropdown-item" style="white-space:normal; padding:0.6rem 1rem; border-bottom:1px solid rgba(0,0,0,0.05); ' + unreadStyle + '">' +
                '<div style="display:flex; gap:0.6rem; align-items:flex-start;">' +
                '<i class="fas ' + escapeHtml(n.icon) + '" style="margin-top:0.2rem; color:#2563eb; font-size:0.8rem; width:14px;"></i>' +
                '<div style="flex:1; min-width:0;">' +
                '<div style="font-weight:600; font-size:0.8rem; color:#1e293b;">' + escapeHtml(n.title) + '</div>' +
                '<div style="font-size:0.75rem; color:#64748b;">' + escapeHtml(n.body) + '</div>' +
                '<div style="font-size:0.68rem; color:#94a3b8; margin-top:0.15rem;">' + escapeHtml(n.created_at) + '</div>' +
                '</div></div></a>';
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

    poll();
    setInterval(poll, 45000);
})();
</script>

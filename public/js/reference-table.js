(function () {
    // Ensure Alpine stores exist and provide helpers for row hover and menu state
    function ensureStore() {
        try {
            if (window.Alpine && typeof Alpine.store === 'function') {
                if (!Alpine.store('menu')) Alpine.store('menu', { openId: null });
                if (!Alpine.store('rowHover')) Alpine.store('rowHover', { hovered: null });
            }
        } catch (e) {
            // noop
        }
    }

    function closeMenus() {
        try {
            if (window.Alpine && typeof Alpine.store === 'function' && Alpine.store('menu')) {
                Alpine.store('menu').openId = null;
            }
        } catch (e) {
            // noop
        }
    }

    function clearRowHover() {
        try {
            if (window.Alpine && typeof Alpine.store === 'function' && Alpine.store('rowHover')) {
                Alpine.store('rowHover').hovered = null;
            }
        } catch (e) {
            // noop
        }
    }

    function validateRowHover() {
        try {
            if (! (window.Alpine && typeof Alpine.store === 'function' && Alpine.store('rowHover'))) return;

            const hovered = Alpine.store('rowHover').hovered;
            if (!hovered) return;
            if (hovered === 'table') return;

            const exists = document.querySelector(`[data-row-key="${hovered}"]`);
            if (!exists) {
                Alpine.store('rowHover').hovered = null;
            }
        } catch (e) {
            // noop
        }
    }

    document.addEventListener('alpine:init', ensureStore);
    ensureStore();

    window.addEventListener('livewire:message', closeMenus);
    window.addEventListener('livewire:update', closeMenus);
    window.addEventListener('livewire:load', closeMenus);
    window.addEventListener('livewire:update', function () { clearRowHover(); validateRowHover(); });
    window.addEventListener('livewire:message', function () { clearRowHover(); validateRowHover(); });

    function syncSearchHeight() {
        try {
            var btn = document.querySelector('.per-page-btn');
            var search = document.querySelector('.search-height-copy');
            if (btn && search) {
                var h = btn.offsetHeight;
                search.style.height = h + 'px';
            }
        } catch (e) {
            // noop
        }
    }

    window.addEventListener('resize', syncSearchHeight);
    window.addEventListener('livewire:update', syncSearchHeight);
    window.addEventListener('livewire:load', syncSearchHeight);
    document.addEventListener('alpine:init', function () { setTimeout(syncSearchHeight, 50); });
    setTimeout(syncSearchHeight, 100);
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'visible') closeMenus(); });

    // Provide a global print helper
    if (typeof window.printReferenceTable !== 'function') {
        window.printReferenceTable = function () {
            try {
                const container = document.querySelector('.reference-table-container');
                if (!container) { alert('Table not found'); return; }

                const wrapper = document.createElement('div');
                wrapper.id = '__print_container';
                wrapper.style.position = 'fixed';
                wrapper.style.left = '0';
                wrapper.style.top = '0';
                wrapper.style.width = '100%';
                wrapper.style.zIndex = '99999';
                wrapper.style.background = 'white';

                const clone = container.cloneNode(true);
                wrapper.appendChild(clone);
                document.body.appendChild(wrapper);

                const style = document.createElement('style');
                style.id = '__print_styles';
                style.innerHTML = "@media print { body * { visibility: hidden !important; } #__print_container, #__print_container * { visibility: visible !important; } #__print_container { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; } }";
                document.head.appendChild(style);

                setTimeout(() => {
                    try {
                        window.print();
                    } finally {
                        setTimeout(() => {
                            const s = document.getElementById('__print_styles'); if (s) s.remove();
                            const w = document.getElementById('__print_container'); if (w) w.remove();
                        }, 500);
                    }
                }, 120);
            } catch (e) {
                console.error(e);
                alert('Unable to print table');
            }
        };
    }
})();

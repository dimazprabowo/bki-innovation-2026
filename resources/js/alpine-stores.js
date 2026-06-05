/**
 * Alpine.js Store Definitions
 * Shared between app and guest layouts to avoid duplication.
 */

document.addEventListener('alpine:init', () => {
    if (!Alpine.store('layout')) {
        Alpine.store('layout', {
            mode: localStorage.getItem('layoutMode') || 'sidebar',
            toggleMode() {
                this.mode = this.mode === 'sidebar' ? 'navbar' : 'sidebar';
                localStorage.setItem('layoutMode', this.mode);
            },
            isSidebar() { return this.mode === 'sidebar'; },
            isNavbar() { return this.mode === 'navbar'; },
        });
    }

    if (!Alpine.store('sidebar')) {
        Alpine.store('sidebar', {
            open: false,
            collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            toggle() { this.open = !this.open; },
            close() { this.open = false; },
            toggleCollapse() {
                this.collapsed = !this.collapsed;
                localStorage.setItem('sidebarCollapsed', this.collapsed);
            },
        });
    }

    if (!Alpine.store('notification')) {
        Alpine.store('notification', {
            queue: [],
            show: false,
            type: 'success',
            title: '',
            message: '',
            timeout: null,

            add(type, message) {
                this.queue.push({ type, message });
                this.showNext();
            },

            showNext() {
                if (this.show || this.queue.length === 0) return;

                const notification = this.queue.shift();
                this.type = notification.type;
                this.title = notification.type === 'success' ? 'Berhasil' : notification.type === 'error' ? 'Error' : notification.type === 'warning' ? 'Peringatan' : 'Informasi';
                this.message = notification.message;
                this.show = true;

                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.show = false;
                    setTimeout(() => this.showNext(), 300);
                }, 5000);
            },

            hide() {
                this.show = false;
                clearTimeout(this.timeout);
                setTimeout(() => this.showNext(), 300);
            }
        });

        // Listen for Livewire notify events
        window.addEventListener('notify', (e) => {
            const notification = Array.isArray(e.detail) ? e.detail[0] : e.detail;
            Alpine.store('notification').add(notification.type || 'success', notification.message || '');
        });
    }
});

/**
 * Dark mode sync — keeps <html> class in sync with localStorage.
 */
function syncDarkMode() {
    const dark = localStorage.getItem('darkMode') === 'true';
    document.documentElement.classList.toggle('dark', dark);
}

syncDarkMode();
document.addEventListener('livewire:navigated', syncDarkMode);
window.addEventListener('storage', function (e) {
    if (e.key === 'darkMode') syncDarkMode();
});

/**
 * Cleanup orphaned x-teleport elements on Livewire SPA navigation.
 * Prevents stuck tooltips/flyouts and layout glitches after wire:navigate.
 */
document.addEventListener('livewire:navigating', () => {
    document.querySelectorAll('body > [x-teleport-target]').forEach(el => el.remove());
    document.querySelectorAll('body > .fixed').forEach(el => {
        if (!el.closest('[wire\\:id]') && !el.closest('.min-h-screen') && !el.matches('[x-data]')) {
            el.remove();
        }
    });
});

/**
 * After Livewire SPA navigation completes, dispatch event to reset Alpine component states.
 */
document.addEventListener('livewire:navigated', () => {
    window.dispatchEvent(new Event('resize'));
});

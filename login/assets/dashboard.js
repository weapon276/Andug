function showLoading(button) {
    const originalContent = button.innerHTML;
    button.classList.add('loading');
    button.innerHTML = '<span class="loading-spinner"></span>';
    
    setTimeout(() => {
        button.classList.remove('loading');
        button.innerHTML = originalContent;
    }, 2000);
}

function toggleNotifications() {
    const panel = document.getElementById('notificationsPanel');
    panel.classList.toggle('active');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('active');
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.notifications-dropdown')) {
        document.getElementById('notificationsPanel').classList.remove('active');
    }
    if (!e.target.closest('.user-menu')) {
        document.getElementById('userDropdown').classList.remove('active');
    }
});

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
}

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            if (item.querySelector('.nav-arrow')) {
                e.stopPropagation();
                item.classList.toggle('expanded');
            }
        });
    });

    // Handle pin/unpin
    sidebar.addEventListener('click', (e) => {
        if (e.target === sidebar || e.target.classList.contains('logo')) {
            sidebar.classList.toggle('pinned');
        }
    });

    sidebar.addEventListener('mouseleave', () => {
        if (!sidebar.classList.contains('pinned')) {
            navItems.forEach(item => {
                item.classList.remove('expanded');
            });
        }
    });
});

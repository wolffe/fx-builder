document.addEventListener('DOMContentLoaded', function () {
    // Click Tab
    document.body.addEventListener('click', function (e) {
        const target = e.target;

        // Check if the clicked element is a nav-tab link
        if (target.matches('#fxb-switcher a.nav-tab')) {
            e.preventDefault();

            // Bail if the clicked tab is already active
            if (target.classList.contains('nav-tab-active')) {
                return false;
            }

            // Confirm switch
            if (!target.classList.contains('switch-confirmed')) {
                const confirmationMessage = target.getAttribute('data-confirm');
                if (!confirm(confirmationMessage)) {
                    return false;
                }
            }

            // Add Confirmed Class
            target.classList.add('switch-confirmed');

            // Force Switch to Visual Editor
            if (typeof window.fxB_switchEditor === 'function') {
                window.fxB_switchEditor('fxb_editor');
            }

            const switcherData = target.getAttribute('data-fxb-switcher');

            if (switcherData === 'editor') {
                document.documentElement.classList.remove('fx_builder_active');
                document.querySelector('input[name="_fxb_active"]').value = '';
                target.classList.add('nav-tab-active');
                const siblings = target.parentElement.querySelectorAll('.nav-tab');
                siblings.forEach(function (sibling) {
                    if (sibling !== target) {
                        sibling.classList.remove('nav-tab-active');
                    }
                });
            } else if (switcherData === 'builder') {
                document.documentElement.classList.add('fx_builder_active');
                document.querySelector('input[name="_fxb_active"]').value = '1';
                target.classList.add('nav-tab-active');
                const siblings = target.parentElement.querySelectorAll('.nav-tab');
                siblings.forEach(function (sibling) {
                    if (sibling !== target) {
                        sibling.classList.remove('nav-tab-active');
                    }
                });
            }
        }
    });
});

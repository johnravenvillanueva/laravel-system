document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                showToast('Please fill in all fields', 'error');
                return;
            }

            // Simulate login - redirect based on role
            // For demo: admin/admin or customer/customer
            if (username === 'admin') {
                window.location.href = '/admin/dashboard';
            } else {
                window.location.href = '/customer/menu';
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const fullName = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;

            if (!fullName || !email || !phone || !password || !confirm) {
                showToast('Please fill in all fields', 'error');
                return;
            }

            if (password !== confirm) {
                showToast('Passwords do not match', 'error');
                return;
            }

            if (password.length < 6) {
                showToast('Password must be at least 6 characters', 'error');
                return;
            }

            showToast('Account created successfully!', 'success');
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
        });
    }
});
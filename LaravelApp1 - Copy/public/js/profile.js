document.addEventListener('DOMContentLoaded', function () {
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const fullName = document.getElementById('profile_name').value.trim();
            const phone = document.getElementById('profile_phone').value.trim();
            const address = document.getElementById('profile_address').value.trim();

            if (!fullName || !phone || !address) {
                showToast('Please fill in all fields', 'error');
                return;
            }

            showToast('Profile updated successfully!', 'success');
        });
    }
});
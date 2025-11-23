    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';

        // Logout function
        function logout() {
            if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
                // Clear localStorage
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('user');

                // Redirect to logout
                window.location.href = `${BASE_URL}/auth/logout`;
            }
        }

        // Load dashboard stats (example)
        async function loadStats() {
            // TODO: Implement API calls to get real stats
            // For now, using placeholder values
        }

        // Verify token on page load
        const token = localStorage.getItem('jwt_token');
        if (!token) {
            window.location.href = `${BASE_URL}/auth/login`;
        }

        // Load stats
        loadStats();
    </script>
    </body>

    </html>
<?php
// We won't use PHP error msg via GET anymore with AJAX, but keep for fallback if needed
$error_msg = '';
if (isset($_GET['error'])) {
    $error_msg = htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Management System - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a8a', // Blue
                        secondary: '#10b981' // Green
                    }
                }
            }
        }
    </script>

    <style>
        body {
            animation: fadeIn 1.5s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .slide-down {
            animation: slideDown 1s ease-out;
        }
        @keyframes slideDown {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal styles */
        #successModal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-600 via-white to-blue-300 flex items-center justify-center">

    <div class="flex w-full max-w-5xl rounded-3xl shadow-2xl overflow-hidden bg-white/30 backdrop-blur-xl slide-down">

        <!-- Left Side Panel (Centered) -->
        <div class="w-1/2 bg-primary text-white p-10 hidden md:flex flex-col items-center justify-center text-center relative">
            <img src="../images/Gaid.png" class="w-24 h-24 mb-6" alt="Barangay Logo">
            <h2 class="text-3xl font-extrabold leading-snug">Welcome to the<br>Barangay Gaid</h2>
            <p class="mt-4 text-lg text-blue-100 leading-relaxed">Magserbisyo kitang tapat sa atong barangay mga dipungal</p>
            <div class="mt-10">
                <p class="text-sm">Need help? Contact your system administrator.</p>
            </div>
        </div>

        <!-- Login Form -->
        <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">
            <div class="text-center mb-8">
                <img src="../images/Gaid.png" class="w-16 h-16 mx-auto rounded-full border-4 border-primary shadow-md" alt="Logo">
                <h1 class="text-3xl font-bold mt-3 text-primary">Barangay Login</h1>
                <p class="text-gray-600 mt-1">Sign in</p>
            </div>

            <form id="loginForm" class="space-y-6" novalidate>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" name="email" required class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary" />
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m0 0l4-4m0 8l-4-4"></path>
                        </svg>
                    </div>
                </div>

                <div>
    <label class="block text-gray-700 font-semibold mb-2">Password</label>
    <div class="relative">
        <input type="password" name="password" id="password" required
            class="w-full pl-10 pr-10 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary" />
        <!-- Lock Icon -->
        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m0-6v2m-6 6a9 9 0 1118 0 9 9 0 01-18 0z" />
        </svg>
        <!-- Toggle Visibility Icon -->
        <button type="button" id="togglePassword"
            class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    </div>
    </div>

                <!-- Error container for AJAX errors -->
                <div id="errorContainer" class="hidden mb-6 p-4 bg-red-600 text-white rounded-lg shadow"></div>

                <button type="submit" class="w-full bg-primary hover:bg-blue-700 text-white font-semibold py-2 rounded-lg shadow transition-all duration-300">
                    Sign In
                </button>

                <div class="flex justify-between mt-4 text-sm text-gray-600">
                    <a href="forgot_password.php" class="hover:underline">Forgot Password?</a>
                    <a href="register.php" class="hover:underline">Create Account</a>
                </div>
            </form>
        </div>

    </div>

    <!-- Modal -->
    <div id="successModal" aria-hidden="true" role="dialog" aria-modal="true" tabindex="-1">
      <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center mx-4">
          <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <h2 class="text-2xl font-semibold mb-2">Login Successful!</h2>
          <p class="text-gray-700 mb-4">Welcome back! Redirecting to your dashboard...</p>
          <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12 mx-auto"></div>
        </div>
      </div>
    </div>

    <style>
      .loader {
        border-top-color: #10B981; /* Tailwind green-500 */
        animation: spin 1s linear infinite;
      }
      @keyframes spin {
        0% { transform: rotate(0deg);}
        100% { transform: rotate(360deg);}
      }
    </style>

    <script>
        const loginForm = document.getElementById('loginForm');
        const errorContainer = document.getElementById('errorContainer');
        const successModal = document.getElementById('successModal');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // prevent full page reload

            // Hide previous errors
            errorContainer.classList.add('hidden');
            errorContainer.textContent = '';

            const formData = new FormData(loginForm);

            fetch('process_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    successModal.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2500);
                } else {
                    errorContainer.textContent = data.message;
                    errorContainer.classList.remove('hidden');
                }
            })
            .catch(() => {
                errorContainer.textContent = "Something went wrong. Please try again.";
                errorContainer.classList.remove('hidden');
            });
        });
    </script>

    <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle icon
        eyeIcon.innerHTML = type === 'password'
            ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`
            : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.638-4.362M15 12a3 3 0 01-3 3m0-6a3 3 0 013 3m6 0a9.978 9.978 0 01-1.284 4.982M3 3l18 18" />`;
    });
</script>


</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forgot Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1e3a8a',
            secondary: '#10b981'
          },
          animation: {
            slideDown: 'slideDown 0.5s ease-out'
          },
          keyframes: {
            slideDown: {
              '0%': { opacity: '0', transform: 'translateY(-20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' }
            }
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-600 via-white to-blue-300 flex items-center justify-center px-4">

  <div class="flex w-full max-w-6xl rounded-3xl shadow-2xl overflow-hidden bg-white/30 backdrop-blur-xl animate-slideDown">
    
    <!-- Left Side -->
    <div class="w-1/2 bg-primary text-white p-10 hidden md:flex flex-col items-center justify-center text-center">
      <img src="../images/Gaid.png" class="w-24 h-24 mb-6" alt="Barangay Logo">
      <h2 class="text-3xl font-extrabold">Reset Your Password</h2>
      <p class="mt-4 text-lg text-blue-100">Secure your account with a new password.</p>
    </div>

    <!-- Forgot Password Form -->
    <div class="w-full md:w-1/2 p-10 overflow-y-auto max-h-[90vh]">
      <div class="text-center mb-8">
        <img src="../images/Gaid.png" class="w-16 h-16 mx-auto rounded-full border-4 border-primary shadow-md" alt="Logo">
        <h1 class="text-3xl font-bold mt-3 text-primary">Forgot Password</h1>
        <p class="text-gray-700 mt-1">Enter your email to receive reset instructions</p>
      </div>

      <form method="POST" action="send_reset_link.php" class="space-y-6">
        <div>
          <label for="email" class="block mb-2 font-semibold text-gray-800">Email Address <span class="text-red-500">*</span></label>
          <input id="email" name="email" type="email" required
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" />
        </div>

        <button type="submit"
          class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition duration-300 shadow-lg">
          Send Reset Link
        </button>

        <div class="text-center">
          <a href="login.php" class="text-sm text-primary hover:underline mt-4 inline-block">Back to Login</a>
        </div>
      </form>
    </div>
  </div>

</body>
</html>

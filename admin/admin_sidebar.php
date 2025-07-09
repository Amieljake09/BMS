<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="fixed top-0 left-0 w-64 h-screen bg-primary text-white flex flex-col py-10 px-6 shadow-2xl z-50">
  <div class="flex items-center space-x-4 mb-10">
    <img src="../images/Gaid.png" class="w-12 h-12 rounded-full border-2 border-white shadow" alt="Logo">
    <div>
      <h2 class="text-lg font-bold">Welcome,</h2>
      <p class="text-sm text-blue-100"><?php echo $_SESSION['full_name']; ?></p>
      <p class="text-xs text-blue-200">Admin</p>
    </div>
  </div>

  <nav class="flex-1 space-y-2 text-sm">
    <a href="admin_dashboard.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'resident_dashboard.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M3 12l2-2m0 0l7-7 7 7m-7-7v18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Dashboard
    </a>

    <a href="account_approval.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'account_approval.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Account Approval
    </a>

    <a href="informations.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'informations.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Informations
    </a>

    <a href="#" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == '#') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M5.121 17.804A3 3 0 007 21h10a3 3 0 002.879-3.804L17.34 9.37a1 1 0 00-.97-.7h-8.74a1 1 0 00-.97.7L5.12 17.804z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      My Profile
    </a>

    <a href="logout.php" class="flex items-center px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 transition font-semibold">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Logout
    </a>
  </nav>
</div>

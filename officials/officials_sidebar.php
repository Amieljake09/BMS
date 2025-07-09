<?php
// Sidebar for Barangay Officials
$current_page = basename($_SERVER['PHP_SELF']);
$showReportsDropdown = ($current_page == 'reports.php' || strpos($current_page, 'report_') !== false);
?>

<!-- Sidebar with Toggle Button -->
<div id="sidebar" class="w-64 bg-primary text-white flex flex-col py-10 px-6 shadow-2xl transition-all duration-300 ease-in-out relative">
  <!-- Toggle Button Inside Sidebar -->
  <button id="toggleSidebar" class="absolute top-4 right-0 transform -translate-x-1/2 bg-primary rounded-full p-1.5 text-white hover:bg-gray-800 z-50">
    <svg id="iconOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
    <svg id="iconClose" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
  </button>

  <!-- Content inside sidebar that will hide when collapsed -->
  <div id="sidebarContent" class="flex-1 overflow-hidden">
    <div class="flex items-center space-x-4 mb-10 ml-2">
      <img src="../images/Gaid.png" class="w-12 h-12 rounded-full border-2 border-white shadow" alt="Logo">
      <div class="opacity-100 transition-opacity duration-300">
        <h2 class="text-lg font-bold">Welcome,</h2>
        <p class="text-sm text-blue-100"><?php echo $_SESSION['full_name']; ?></p>
        <p class="text-xs text-blue-200">Barangay Official</p>
      </div>
    </div>

    <nav class="space-y-2 text-sm">

      <!-- Dashboard -->
      <a href="officials_dashboard.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'officials_dashboard.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M3 12l2-2m0 0l7-7 7 7m-7-7v18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="whitespace-nowrap">Dashboard</span>
      </a>

       <!-- officials and comittee -->
      <a href="officials_committees.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'officials_committees.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
  <svg class="w-5 h-5 mr-2 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M17 20h5v-2a4 4 0 00-5-3.87M3 20h5v-2a4 4 0 00-5-3.87M12 4a4 4 0 110 8 4 4 0 010-8z"/>
  </svg>
  <span class="whitespace-nowrap">Officials & Committees</span>
</a>


      <!-- Document Approvals -->
      <a href="document_approvals.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'document_approvals.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M9 17v-6h13v6m-8-6V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="whitespace-nowrap">Document Approvals</span>
      </a>

      <!-- community reports -->
<a href="community_reports.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'community_reports.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M9 12h6M9 16h6M12 8h.01M4 6h16M4 20h16M4 6v14a2 2 0 002 2h12a2 2 0 002-2V6"/>
  </svg>
  <span class="whitespace-nowrap">Community Reports</span>
</a>

      <!-- Reports Dropdown -->
      <div class="relative">
        <button id="reportsDropdownBtn" class="flex items-center w-full px-4 py-3 rounded-lg 
          <?php echo $showReportsDropdown ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?>
          transition focus:outline-none">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h18v18H3V3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3 9h18M9 21V9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="whitespace-nowrap">Statistical Reports</span>
          <svg class="ml-auto w-4 h-4 transition-transform transform <?php echo $showReportsDropdown ? 'rotate-180' : ''; ?>" id="dropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <div id="reportsDropdownMenu" class="<?php echo $showReportsDropdown ? '' : 'hidden'; ?> mt-1 space-y-1 pl-6 bg-blue-800 bg-opacity-50 rounded-md">
          <a href="report_document_related.php" class="block px-4 py-2 text-sm rounded-lg 
            <?php echo ($current_page == 'report_document_related.php') ? 'bg-white text-primary font-semibold' : 'hover:bg-white hover:text-primary'; ?> transition">
            Document-related Reports
          </a>
          <a href="report_population.php" class="block px-4 py-2 text-sm rounded-lg 
            <?php echo ($current_page == 'report_population.php') ? 'bg-white text-primary font-semibold' : 'hover:bg-white hover:text-primary'; ?> transition">
            Barangay Population Reports
          </a>
          <a href="report_activities.php" class="block px-4 py-2 text-sm rounded-lg 
            <?php echo ($current_page == 'report_activities.php') ? 'bg-white text-primary font-semibold' : 'hover:bg-white hover:text-primary'; ?> transition">
            Activity Reports
          </a>
        </div>
      </div>

      <!-- resident management -->
<a href="resident_management.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'resident_management.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M17 20h5v-2a4 4 0 00-5-3.87M3 20h5v-2a4 4 0 00-5-3.87M12 4a4 4 0 110 8 4 4 0 010-8z"/>
  </svg>
  <span class="whitespace-nowrap">Resident Management</span>
</a>

      
      <!-- households -->
<a href="households.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'households.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M3 10l9-7 9 7v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M9 21V9h6v12"/>
  </svg>
  <span class="whitespace-nowrap">Households</span>
</a>

      <!-- blotters & reports -->
<a href="blotters.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'blotters.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M8 16h8M8 12h8M9 8h6M12 2a10 10 0 100 20 10 10 0 000-20z"/>
  </svg>
  <span class="whitespace-nowrap">Blotters & Reports</span>
</a>

      <!-- emergency contact -->
<a href="contacts.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'contacts.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
  <svg class="w-5 h-5 mr-2 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M22 16.92v3a2 2 0 01-2.18 2A19.91 19.91 0 013 5.18
         2 2 0 015 3h3a2 2 0 012 1.72
         13.35 13.35 0 001.03 3.09
         2 2 0 01-.45 2.11L9.91 11
         a16 16 0 006.09 6.09l1.08-1.08
         a2 2 0 012.11-.45
         13.35 13.35 0 003.09 1.03
         A2 2 0 0122 16.92z"/>
  </svg>
  <span class="whitespace-nowrap">Emergency Contact</span>
</a>

      <!-- programs and projects -->
<a href="programs.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'programs.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
  <svg class="w-5 h-5 mr-2 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M3 3v18h18M7 14l3-3 4 4 5-5"/>
  </svg>
  <span class="whitespace-nowrap">Program & Projects</span>
</a>

      <!-- Announcements -->
      <a href="announcement.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'announcement.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-5-5.917V5a3 3 0 10-6 0v.083A6.002 6.002 0 002 11v3.159c0 .538-.214 1.055-.595 1.436L0 17h5m10 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="whitespace-nowrap">Announcements</span>
      </a>

      <!-- Profile -->
      <a href="officials_profile.php" class="flex items-center px-4 py-3 rounded-lg <?php echo ($current_page == 'officials_profile.php') ? 'bg-white text-primary font-semibold shadow' : 'hover:bg-white hover:text-primary hover:font-semibold'; ?> transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M5.121 17.804A3 3 0 007 21h10a3 3 0 002.879-3.804L17.34 9.37a1 1 0 00-.97-.7h-8.74a1 1 0 00-.97.7L5.12 17.804z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="whitespace-nowrap">My Profile</span>
      </a>

      <!-- Logout -->
      <a href="logout.php" class="flex items-center px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 transition font-semibold">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="whitespace-nowrap">Logout</span>
      </a>
    </nav>
  </div>
</div>

<!-- JavaScript -->
<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  const sidebarContent = document.getElementById('sidebarContent');
  const iconOpen = document.getElementById('iconOpen');
  const iconClose = document.getElementById('iconClose');

  // Sidebar Toggle
  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('w-16'); // Minimal width
    sidebar.classList.toggle('w-64'); // Full width
    sidebarContent.classList.toggle('hidden'); // Hide content
    iconOpen.classList.toggle('hidden');
    iconClose.classList.toggle('hidden');
  });

  // Reports Dropdown Toggle
  const reportsDropdownBtn = document.getElementById('reportsDropdownBtn');
  const reportsDropdownMenu = document.getElementById('reportsDropdownMenu');
  const dropdownArrow = document.getElementById('dropdownArrow');

  reportsDropdownBtn.addEventListener('click', function(e) {
    e.preventDefault(); // Prevent accidental redirect
    const isHidden = reportsDropdownMenu.classList.contains('hidden');
    reportsDropdownMenu.classList.toggle('hidden');
    dropdownArrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
  });
</script>
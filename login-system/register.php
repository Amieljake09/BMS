<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Barangay Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1e3a8a',
            secondary: '#10b981'
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
    <h2 class="text-3xl font-extrabold">Register to the Barangay System</h2>
    <p class="mt-4 text-lg text-blue-100">Ensure your barangay stays connected and organized.</p>
  </div>


    <!-- Registration Form -->
    <div class="w-full md:w-1/2 p-10 overflow-y-auto max-h-[90vh]">
      <div class="text-center mb-8">
        <img src="../images/Gaid.png" class="w-16 h-16 mx-auto rounded-full border-4 border-primary shadow-md" alt="Logo">
        <h1 class="text-3xl font-bold mt-3 text-primary">Create Your Account</h1>
        <p class="text-gray-700 mt-1">Join as a Resident or Barangay Official</p>
      </div>

      <form method="POST" action="register_process.php" id="registrationForm" enctype="multipart/form-data" class="space-y-6">
        <!-- Account Info -->
        <div>
          <label for="full_name" class="block mb-2 font-semibold text-gray-800">Full Name <span class="text-red-500">*</span></label>
          <input id="full_name" name="full_name" type="text" required
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" />
        </div>

        <div>
          <label for="email" class="block mb-2 font-semibold text-gray-800">Email Address</label>
          <input id="email" name="email" type="email"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" />
        </div>

        <!-- password field -->
       <div class="relative mt-4">
  <label for="password" class="block mb-2 font-semibold text-gray-800">
    Password <span class="text-red-500">*</span>
  </label>

  <input
    id="password"
    name="password"
    type="password"
    required
    oninput="checkPasswordStrength(this.value)"
    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
  />

  <!-- Eye Icon Button -->
  <button
    type="button"
    onclick="togglePasswordVisibility('password', this)"
    class="absolute top-10 right-3 text-gray-600 hover:text-gray-900 focus:outline-none"
    aria-label="Toggle password visibility"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
  </button>

  <!-- Password Strength Indicator -->
  <div class="mt-2 h-1 w-full bg-gray-200 rounded-full overflow-hidden">
    <div id="strengthBar" class="h-full w-0 bg-transparent transition-all duration-300"></div>
  </div>

  <!-- Feedback Text -->
  <p id="passwordFeedback" class="mt-1 text-sm text-gray-600">Enter a password to see strength</p>
</div>

<div class="relative mt-4">
  <label for="confirm_password" class="block mb-2 font-semibold text-gray-800">
    Confirm Password <span class="text-red-500">*</span>
  </label>
  <input id="confirm_password" name="confirm_password" type="password" required oninput="checkPasswordMatch()"
    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
  />
  <button
    type="button"
    onclick="togglePasswordVisibility('confirm_password', this)"
    class="absolute top-10 right-3 text-gray-600 hover:text-gray-900 focus:outline-none"
    aria-label="Toggle confirm password visibility"
  >
    <!-- Eye icon SVG -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-6 w-6"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
      />
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
      />
    </svg>
  </button>
</div>

         <!-- Role Select -->
      <div>
        <label for="role" class="block mb-2 font-semibold text-gray-800">
          Register As <span class="text-red-500">*</span>
        </label>
        <select
          id="role"
          name="role"
          required
          onchange="toggleRoleFields()"
          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
        >
          <option value="">Select Role</option>
          <option value="Resident">Resident</option>
          <option value="Official">Barangay Official</option>
          <option value="Staff">Barangay Staff</option>
        </select>
      </div>

      <!-- Resident Fields -->
      <div id="residentFields" class="hidden space-y-6">
        <h3
          class="text-2xl font-semibold text-blue-600 mb-6 border-b-2 border-blue-600 pb-2"
        >
          Resident Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="dob" class="block mb-2 font-semibold text-gray-700">Date of Birth</label>
            <input
              id="dob"
              name="dob"
              type="date"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="pob" class="block mb-2 font-semibold text-gray-700">Place of Birth</label>
            <input
              id="pob"
              name="pob"
              type="text"
              placeholder="Place of Birth"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="age" class="block mb-2 font-semibold text-gray-700">Age</label>
            <input
              id="age"
              name="age"
              type="number"
              placeholder="Age"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="gender" class="block mb-2 font-semibold text-gray-700">Gender</label>
            <select
              id="gender"
              name="gender"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            >
              <option value="">Select Gender</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </div>
          <div>
            <label for="civil_status" class="block mb-2 font-semibold text-gray-700">Civil Status</label>
            <select
              id="civil_status"
              name="civil_status"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            >
              <option value="">Select Civil Status</option>
              <option>Single</option>
              <option>Married</option>
              <option>Widow/Widower</option>
              <option>Separated</option>
            </select>
          </div>

          <!-- Employment Status -->
<div>
    <label for="employment_status" class="block mb-2 font-semibold text-gray-700">Employment Status</label>
    <select id="employment_status" name="employment_status"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
        <option value="">Select Employment Status</option>
        <option value="Employed">Employed</option>
        <option value="Unemployed">Unemployed</option>
        <option value="Self-Employed">Self-Employed</option>
        <option value="Student">Student</option>
        <option value="Retired">Retired</option>
    </select>
</div>
          <div>
            <label for="nationality" class="block mb-2 font-semibold text-gray-700">Nationality</label>
            <input
              id="nationality"
              name="nationality"
              type="text"
              placeholder="Nationality"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="religion" class="block mb-2 font-semibold text-gray-700">Religion (optional)</label>
            <input
              id="religion"
              name="religion"
              type="text"
              placeholder="Religion"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="address" class="block mb-2 font-semibold text-gray-700">Present Address</label>
            <input
              id="address"
              name="address"
              type="text"
              placeholder="Present Address"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="phone" class="block mb-2 font-semibold text-gray-700">Phone/Mobile Number</label>
            <input
              id="phone"
              name="phone"
              type="text"
              placeholder="Phone Number"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="res_email" class="block mb-2 font-semibold text-gray-700">Email Address (optional)</label>
            <input
              id="res_email"
              name="res_email"
              type="email"
              placeholder="Email Address"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="resident_type" class="block mb-2 font-semibold text-gray-700">Resident Type</label>
            <select
              id="resident_type"
              name="resident_type"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            >
              <option value="">Select Resident Type</option>
              <option>Permanent</option>
              <option>Temporary</option>
              <option>Voter</option>
              <option>Non-Voter</option>
            </select>
          </div>
          <div>
            <label for="stay_length" class="block mb-2 font-semibold text-gray-700">Length of Stay in Barangay</label>
            <input
              id="stay_length"
              name="stay_length"
              type="text"
              placeholder="e.g. 5 years"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
  <label for="proof" class="block mb-2 font-semibold text-gray-700">Proof of Residency</label>
  <input id="proof" name="proof" type="file"
    accept="image/*,application/pdf"
    required
    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
  />
  <p class="mt-1 text-sm text-gray-500">Supported formats: JPG, PNG, PDF</p>
</div>
        </div>
      </div>

      <!-- Official Fields -->
      <div id="officialFields" class="hidden space-y-6">
        <h3
          class="text-2xl font-semibold text-blue-600 mb-6 border-b-2 border-blue-600 pb-2"
        >
          Barangay Official Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="dob_off" class="block mb-2 font-semibold text-gray-700">Date of Birth</label>
            <input
              id="dob_off"
              name="dob_off"
              type="date"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="pob_off" class="block mb-2 font-semibold text-gray-700">Place of Birth</label>
            <input
              id="pob_off"
              name="pob_off"
              type="text"
              placeholder="Place of Birth"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="age_off" class="block mb-2 font-semibold text-gray-700">Age</label>
            <input
              id="age_off"
              name="age_off"
              type="number"
              placeholder="Age"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="gender_off" class="block mb-2 font-semibold text-gray-700">Gender</label>
            <select
              id="gender_off"
              name="gender_off"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            >
              <option value="">Select Gender</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </div>
          <div>
            <label for="civil_status_off" class="block mb-2 font-semibold text-gray-700">Civil Status</label>
            <select
              id="civil_status_off"
              name="civil_status_off"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            >
              <option value="">Select Civil Status</option>
              <option>Single</option>
              <option>Married</option>
              <option>Widow/Widower</option>
              <option>Separated</option>
            </select>
          </div>
          <div>
            <label for="nationality_off" class="block mb-2 font-semibold text-gray-700">Nationality</label>
            <input
              id="nationality_off"
              name="nationality_off"
              type="text"
              placeholder="Nationality"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="religion_off" class="block mb-2 font-semibold text-gray-700">Religion (optional)</label>
            <input
              id="religion_off"
              name="religion_off"
              type="text"
              placeholder="Religion"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="position" class="block mb-2 font-semibold text-gray-700">Position</label>
            <input
              id="position"
              name="position"
              type="text"
              placeholder="Position"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="term_start" class="block mb-2 font-semibold text-gray-700">Term Start</label>
            <input
              id="term_start"
              name="term_start"
              type="date"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="term_end" class="block mb-2 font-semibold text-gray-700">Term End</label>
            <input
              id="term_end"
              name="term_end"
              type="date"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="address_off" class="block mb-2 font-semibold text-gray-700">Present Address</label>
            <input
              id="address_off"
              name="address_off"
              type="text"
              placeholder="Present Address"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="phone_off" class="block mb-2 font-semibold text-gray-700">Phone/Mobile Number</label>
            <input
              id="phone_off"
              name="phone_off"
              type="text"
              placeholder="Phone Number"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
          <div>
            <label for="email_off" class="block mb-2 font-semibold text-gray-700">Email Address (optional)</label>
            <input
              id="email_off"
              name="email_off"
              type="email"
              placeholder="Email Address"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
            />
          </div>
        </div>
      </div>

      <!-- Staff Fields -->
<div id="staffFields" class="hidden space-y-6">
  <h3 class="text-2xl font-semibold text-blue-600 mb-6 border-b-2 border-blue-600 pb-2">
    Barangay Staff Information
  </h3>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Complete Address -->
    <div>
      <label for="complete_address" class="block mb-2 font-semibold text-gray-700">Complete Address</label>
      <input
        id="complete_address"
        name="complete_address"
        type="text"
        placeholder="House No./Purok/Street, Barangay, City/Municipality, Province"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      />
    </div>
    <!-- Phone Number -->
    <div>
      <label for="phone_staff" class="block mb-2 font-semibold text-gray-700">Phone Number</label>
      <input
        id="phone_staff"
        name="phone_staff"
        type="text"
        placeholder="Phone Number"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      />
    </div>
    <!-- Email Address -->
    <div>
      <label for="email_staff" class="block mb-2 font-semibold text-gray-700">Email Address</label>
      <input
        id="email_staff"
        name="email_staff"
        type="email"
        placeholder="Email Address"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      />
    </div>
    <!-- Position / Office -->
    <div>
      <label for="position_staff" class="block mb-2 font-semibold text-gray-700">Position/Office</label>
      <select
        id="position_staff"
        name="position_staff"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      >
        <option value="">Select Position</option>
        <option>Barangay Secretary</option>
        <option>Barangay Treasurer</option>
        <option>Tanod</option>
        <option>Cleaner</option>
        <option>Utility Worker</option>
      </select>
    </div>
    <!-- Date Started -->
    <div>
      <label for="date_started" class="block mb-2 font-semibold text-gray-700">Date Started</label>
      <input
        id="date_started"
        name="date_started"
        type="date"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      />
    </div>
  </div>
</div>

<!-- Admin Fields -->
<div id="adminFields" class="hidden space-y-6">
  <h3 class="text-2xl font-semibold text-blue-600 mb-6 border-b-2 border-blue-600 pb-2">
    Admin Information
  </h3>
  <div class="grid grid-cols-1 gap-6">
    <div>
      <label for="admin_position" class="block mb-2 font-semibold text-gray-700">Position / Role</label>
      <input
        id="admin_position"
        name="admin_position"
        type="text"
        placeholder="e.g., System Administrator"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      />
    </div>
    <div>
      <label for="admin_phone" class="block mb-2 font-semibold text-gray-700">Phone Number</label>
      <input
        id="admin_phone"
        name="admin_phone"
        type="text"
        placeholder="Phone Number"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      />
    </div>
    <div>
      <label for="admin_email" class="block mb-2 font-semibold text-gray-700">Email Address</label>
      <input
        id="admin_email"
        name="admin_email"
        type="email"
        placeholder="Email Address"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
      />
    </div>
  </div>
</div>

      <!-- Submit Button -->
      <div class="text-center">
        <button
          type="submit"
          class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">
          Register
        </button>
      </div>

      <p class="text-center mt-6 text-gray-700 text-sm">
  Already have an account?
  <a href="login.php" class="text-blue-700 hover:underline font-semibold">Login here</a>
</p>

    </form>
  </div>

  <style>
    /* Slide down animation */
    @keyframes slideDown {
      0% {
        opacity: 0;
        transform: translateY(-20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .animate-slideDown {
      animation: slideDown 0.5s ease forwards;
    }
  </style>

<script>
function toggleRoleFields() {
  const role = document.getElementById("role").value;
  const residentFields = document.getElementById("residentFields");
  const officialFields = document.getElementById("officialFields");
  const staffFields = document.getElementById("staffFields");
  const adminFields = document.getElementById("adminFields");

  // Hide all by default
  residentFields.classList.add("hidden");
  officialFields.classList.add("hidden");
  staffFields.classList.add("hidden");
  adminFields.classList.add("hidden");

  // Show relevant section
  if (role === "Resident") {
    residentFields.classList.remove("hidden");
    residentFields.classList.add("animate-slideDown");
  } else if (role === "Official") {
    officialFields.classList.remove("hidden");
    officialFields.classList.add("animate-slideDown");
  } else if (role === "Staff") {
    staffFields.classList.remove("hidden");
    staffFields.classList.add("animate-slideDown");
  } else if (role === "Admin") {
    adminFields.classList.remove("hidden");
    adminFields.classList.add("animate-slideDown");
  }
}
</script>


<script>
function togglePasswordVisibility(fieldId, btn) {
  const input = document.getElementById(fieldId);
  if (input.type === "password") {
    input.type = "text";
    btn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.963 9.963 0 012.225-3.451m1.956-1.955A9.957 9.957 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.977 9.977 0 01-3.032 4.07M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
      </svg>`;
  } else {
    input.type = "password";
    btn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
      </svg>`;
  }
}

function checkPasswordStrength(password) {
  const strengthBar = document.getElementById("strengthBar");
  const feedback = document.getElementById("passwordFeedback");

  let strength = 0;

  if (password.length >= 8) strength++;
  if (/[A-Z]+/.test(password)) strength++;
  if (/[a-z]+/.test(password)) strength++;
  if (/[0-9]+/.test(password)) strength++;
  if (/[^A-Za-z0-9]+/.test(password)) strength++;

  // Update UI
  switch (strength) {
    case 0:
    case 1:
      strengthBar.style.width = "20%";
      strengthBar.className = "h-full bg-red-500";
      feedback.textContent = "Too Short";
      feedback.className = "mt-1 text-sm text-red-500";
      break;
    case 2:
      strengthBar.style.width = "40%";
      strengthBar.className = "h-full bg-yellow-500";
      feedback.textContent = "Weak";
      feedback.className = "mt-1 text-sm text-yellow-600";
      break;
    case 3:
      strengthBar.style.width = "60%";
      strengthBar.className = "h-full bg-blue-500";
      feedback.textContent = "Good";
      feedback.className = "mt-1 text-sm text-blue-600";
      break;
    case 4:
      strengthBar.style.width = "80%";
      strengthBar.className = "h-full bg-green-500";
      feedback.textContent = "Strong";
      feedback.className = "mt-1 text-sm text-green-600";
      break;
    case 5:
      strengthBar.style.width = "100%";
      strengthBar.className = "h-full bg-green-600";
      feedback.textContent = "Very Strong";
      feedback.className = "mt-1 text-sm text-green-700";
      break;
  }

  if (password === "") {
    strengthBar.style.width = "0%";
    feedback.textContent = "Enter a password to see strength";
    feedback.className = "mt-1 text-sm text-gray-600";
  }
}
</script>

<script>

  function checkPasswordMatch() {
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;
  const errorContainer = document.getElementById("passwordError");

  if (password !== confirmPassword && confirmPassword !== "") {
    if (!errorContainer) {
      const errorMsg = document.createElement("div");
      errorMsg.id = "passwordError";
      errorMsg.className = "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4";
      errorMsg.role = "alert";
      errorMsg.innerHTML = `
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">Passwords do not match.</span>
      `;
      const form = document.getElementById("registrationForm");
      form.prepend(errorMsg);
    }
  } else {
    if (errorContainer) {
      errorContainer.remove();
    }
  }
}


</script>



</body>
</html>

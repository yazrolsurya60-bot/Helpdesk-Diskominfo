<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>PINTAS - Login</title>
  
  <!-- Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#1e3a8a',
              hover: '#172554',
              light: '#eff6ff',
            }
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            headline: ['"Plus Jakarta Sans"', 'sans-serif'],
          }
        }
      }
    }
  </script>
  
  <!-- Font Awesome Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800 antialiased selection:bg-primary selection:text-white">
  
  <!-- BEGIN: MainContainer Card -->
  <main class="w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-w-[490px] my-8">
    <section class="w-full p-6 sm:p-10 lg:p-12 flex flex-col justify-between bg-white z-10" data-purpose="auth-form-container">
      <div class="max-w-[420px] w-full mx-auto">
        
        <!-- Logo Monogram 'P' Biru Navy -->
        <div class="flex justify-center mb-6">
          <div class="w-16 h-16 rounded-2xl bg-blue-50/80 border border-blue-100 flex items-center justify-center shadow-sm" data-purpose="brand-logo">
            <span class="text-3xl font-black font-headline text-primary tracking-tight select-none">P</span>
          </div>
        </div>

        <!-- Heading Text -->
        <div class="text-center mb-6">
          <h1 class="text-2xl sm:text-3xl font-bold font-headline tracking-tight text-gray-900">PINTAS</h1>
          <p class="text-xs sm:text-sm text-gray-500 mt-1.5 font-normal leading-relaxed">
            Penghubung Informasi dan Tanggapan melalui Aplikasi Singkat
          </p>
        </div>

        <!-- Error Alert -->
        @if ($errors->any())
          <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-700 text-xs flex items-center gap-2.5 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-rose-500 shrink-0 text-sm"></i>
            <span class="font-medium">{{ $errors->first() }}</span>
          </div>
        @endif

        <!-- Form Autentikasi -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4 mb-6">
          @csrf
          
          <!-- Email Input -->
          <div>
            <label class="block text-xs font-bold text-gray-800 mb-1.5 uppercase tracking-wider" for="email">Email</label>
            <div class="relative flex items-center">
              <span class="absolute left-3.5 text-gray-400 text-sm pointer-events-none">
                <i class="fa-regular fa-envelope"></i>
              </span>
              <input 
                id="email" 
                name="email" 
                type="email" 
                value="{{ old('email') }}" 
                required 
                autocomplete="email"
                placeholder="masukkan email Anda" 
                class="w-full text-xs sm:text-sm rounded-xl border @error('email') border-rose-400 bg-rose-50/20 @else border-gray-200 bg-white @enderror pl-10 pr-10 py-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium transition-colors"
                oninput="validateEmail(this)"
              />
              <span id="emailCheck" class="absolute right-3.5 text-emerald-500 text-sm hidden pointer-events-none">
                <i class="fa-regular fa-circle-check"></i>
              </span>
            </div>
          </div>

          <!-- Password Input -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider" for="password">Password</label>
            </div>
            <div class="relative flex items-center">
              <span class="absolute left-3.5 text-gray-400 text-sm pointer-events-none">
                <i class="fa-solid fa-lock text-xs"></i>
              </span>
              <input 
                id="password" 
                name="password" 
                type="password" 
                required 
                autocomplete="current-password"
                placeholder="masukkan kata sandi" 
                class="w-full text-xs sm:text-sm rounded-xl border @error('password') border-rose-400 bg-rose-50/20 @else border-gray-200 bg-white @enderror pl-10 pr-10 py-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium transition-colors"
              />
              <button 
                type="button" 
                onclick="togglePasswordVisibility()" 
                class="absolute right-3.5 text-gray-400 hover:text-gray-600 text-sm focus:outline-none transition-colors"
                aria-label="Toggle password visibility"
              >
                <i id="eyeIcon" class="fa-regular fa-eye-slash"></i>
              </button>
            </div>
          </div>

          <!-- Submit Button Biru Navy -->
          <button 
            type="submit" 
            class="w-full mt-2 py-3 px-4 bg-primary hover:bg-primary-hover text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-900/20 hover:shadow-blue-900/30 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]"
          >
            <span>Sign In</span>
            <i class="fa-regular fa-circle-right text-xs"></i>
          </button>
        </form>

      </div>

      <!-- Legal / Instansi Footer -->
      <footer class="text-center pt-4 border-t border-gray-100" data-purpose="auth-footer">
        <p class="text-[11px] text-gray-400 font-medium">
          Dinas Komunikasi dan Informatika Provinsi Kalimantan Barat
        </p>
      </footer>
    </section>
  </main>
  <!-- END: MainContainer Card -->

  <!-- Interaktivitas UI Client-Side -->
  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      if (!passwordInput || !eyeIcon) return;

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    }

    function validateEmail(input) {
      const check = document.getElementById('emailCheck');
      if (!check) return;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (emailRegex.test(input.value.trim())) {
        check.classList.remove('hidden');
      } else {
        check.classList.add('hidden');
      }
    }

    // Periksa email saat load awal jika terdapat old input
    document.addEventListener('DOMContentLoaded', function() {
      const emailInput = document.getElementById('email');
      if (emailInput && emailInput.value) {
        validateEmail(emailInput);
      }
    });
  </script>
</body>
</html>

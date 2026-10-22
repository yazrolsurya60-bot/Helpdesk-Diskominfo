<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Nexus CRM - Login</title>
  <!-- Font Setup -->
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap"
    rel="stylesheet" />
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <!-- Tailwind Configuration -->
  <script data-purpose="tailwind-config">
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            primary: {
              DEFAULT: '#1e3a8a',
              hover: '#1e40af',
            },
            background: '#f8fafc',
            surface: '#ffffff',
            text: {
              main: '#0f172a',
              muted: '#64748b'
            },
            border: '#e2e8f0'
          },
          borderRadius: {
            'custom': '0.5rem', // ROUND_FOUR equivalent roughly
          }
        }
      }
    }
  </script>
  <!-- Custom Styles -->
  <style data-purpose="custom-utilities">
    body {
      background-color: theme('colors.background');
      color: theme('colors.text.main');
    }

    .input-field {
      @apply w-full px-4 py-2.5 rounded-custom border border-border bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm;
    }

    .btn-primary {
      @apply w-full px-4 py-2.5 bg-primary hover:bg-primary-hover text-white font-medium rounded-custom transition-colors flex items-center justify-center;
    }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
  <!-- BEGIN: Login Container -->
  <main class="w-full max-w-md">
    <!-- Branding Header -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center p-2 bg-primary rounded-custom mb-4">
        <!-- Logo Icon (Simplified headset/cloud based on dashboard) -->
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
          stroke-width="2" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M3 15h18"></path>
          <path d="M6 10a6 6 0 0 1 12 0v5"></path>
          <path d="M8 15v2a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2"></path>
        </svg>
      </div>
      <h1 class="text-3xl font-bold text-text-main">PINTAS - Penghubung Informasi dan Tanggapan melalui Aplikasi Singkat
      </h1>
      <p class="text-text-muted mt-2">Enterprise Helpdesk</p>
    </div>
    <!-- Login Card -->
    <div class="bg-surface rounded-custom shadow-sm border border-border p-8">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-text-main">Sign In</h2>
        <p class="text-sm text-text-muted mt-1">Please enter your credentials to access your account.</p>
      </div>
      <form action="{{ route('login') }}" class="space-y-5" method="POST">
        @csrf
        <!-- Email Input -->
        <div>
          <label class="block text-sm font-medium text-text-main mb-1.5" for="email">Email Address</label>

          <input class="input-field @error('email') border-red-500 @enderror" id="email" name="email"
            value="{{ old('email') }}" placeholder="name@company.com" required="" type="email" />
          @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror

        </div>
        <!-- Password Input -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-sm font-medium text-text-main" for="password">Password</label>
            <a class="text-sm text-primary hover:underline font-medium" href="#">Forgot Password?</a>
          </div>

          <input class="input-field @error('password') border-red-500 @enderror" id="password" name="password"
            placeholder="••••••••" required="" type="password" />
          @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror

        </div>
        <!-- Submit Button -->
        <div class="pt-2">
          <button class="btn-primary" type="submit">
            Sign In
          </button>
        </div>
      </form>
    </div>
  </main>
  <!-- END: Login Container -->
</body>

</html>
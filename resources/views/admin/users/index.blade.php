@extends('layouts.admin')
@section('title', 'Manajemen User')
@section('content')
  <div class="flex flex-col w-full">
    <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full">
      <!-- 1. Header Title & Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
          <div class="inline-flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-primary">
              ADMINISTRASI SISTEM • AKSES &amp; OTORISASI
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg text-on-surface tracking-tight mt-1">
            Manajemen User <span class="sr-only">User Management</span>
          </h1>
          <p class="text-sm font-body-md text-on-surface-variant max-w-2xl">
            Kelola data pengguna sistem, penetapan hak akses, dan distribusi akun helpdesk PINTAS.
          </p>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-auto">
          <button
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-on-primary font-headline-sm text-headline-sm font-semibold shadow-sm hover:bg-[#172554] transition-all active:scale-[0.98] cursor-pointer"
            id="addUserBtn" type="button">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            <span>Tambah Pengguna</span>
          </button>
        </div>
      </div>

      <!-- 2. Metric Summary KPI Cards (Grid 4 Kolom Sesuai Standar Front Desk) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Card 1: Total Pengguna -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
              Total User
            </span>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
              <span class="material-symbols-outlined text-[22px]">group</span>
            </div>
          </div>
          <div class="mt-4 flex items-baseline">
            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
              {{ $users->count() }}
            </span>
          </div>
        </div>

        <!-- Card 2: Administrator -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
              Administrator
            </span>
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
              <span class="material-symbols-outlined text-[22px]">security</span>
            </div>
          </div>
          <div class="mt-4 flex items-baseline">
            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
              {{ $users->where('role', 'admin')->count() }}
            </span>
          </div>
        </div>

        <!-- Card 3: Front Desk -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
              Front Desk
            </span>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
              <span class="material-symbols-outlined text-[22px]">support_agent</span>
            </div>
          </div>
          <div class="mt-4 flex items-baseline">
            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
              {{ $users->where('role', 'front_desk')->count() }}
            </span>
          </div>
        </div>

        <!-- Card 4: Staf Bidang / Teknisi -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
              Staf Bidang
            </span>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
              <span class="material-symbols-outlined text-[22px]">engineering</span>
            </div>
          </div>
          <div class="mt-4 flex items-baseline">
            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
              {{ $users->where('role', 'staf_bidang')->count() }}
            </span>
          </div>
        </div>
      </div>

      <!-- 3. Main Table Card Container (Satu Kartu Terpadu Sesuai Aturan Front Desk) -->
      <div
        class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/40 overflow-hidden flex flex-col w-full">
        <!-- Table Toolbar Header -->
        <div
          class="p-4 bg-surface-container-low/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-outline-variant/40">
          <div class="flex flex-col sm:flex-row flex-1 items-stretch sm:items-center gap-3 w-full">
            <!-- Search input -->
            <div class="relative w-full sm:w-80">
              <span
                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
              <input
                class="w-full pl-9 pr-8 py-2 bg-surface-container-lowest rounded-lg border border-outline-variant/50 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary shadow-xs transition-colors"
                id="tableSearchInput" placeholder="Cari nama, email, atau peran..." type="text" />
            </div>
          </div>

          <div class="flex items-center gap-2 self-end sm:self-auto">
            <span
              class="px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-semibold">
              Total: {{ $users->count() }} Pengguna Terdaftar
            </span>
          </div>
        </div>

        <!-- Data Table -->
        <div class="w-full overflow-x-auto">
          <table class="w-full min-w-[750px] text-left border-collapse" id="usersTable">
            <thead>
              <tr
                class="bg-surface-container-low/60 text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-outline-variant/40">
                <th class="py-3.5 px-5 font-semibold" scope="col">Nama &amp; Kontak</th>
                <th class="py-3.5 px-5 font-semibold" scope="col">Unit Kerja / Bidang</th>
                <th class="py-3.5 px-5 font-semibold" scope="col">Peran Akses</th>
                <th class="py-3.5 px-5 font-semibold" scope="col">Waktu Terdaftar</th>
                <th class="py-3.5 px-5 text-right font-semibold" scope="col">Aksi</th>
              </tr>
            </thead>
            <tbody class="font-body-sm text-body-sm text-on-surface divide-y divide-outline-variant/20">
              @forelse($users as $user)
                <tr class="hover:bg-primary/5 transition-colors user-row">
                  <!-- Kolom Pengguna -->
                  <td class="py-4 px-5 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-9 h-9 rounded-full bg-primary-container text-on-primary-container font-headline-sm text-headline-sm font-bold flex items-center justify-center shrink-0 shadow-xs">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                      </div>
                      <div class="flex flex-col min-w-0">
                        <span class="font-semibold text-on-surface truncate">{{ $user->name }}</span>
                        <span class="text-xs text-on-surface-variant flex items-center gap-1 truncate mt-0.5">
                          <span class="material-symbols-outlined text-[14px]">mail</span>
                          {{ $user->email }}
                        </span>
                      </div>
                    </div>
                  </td>

                  <!-- Kolom Unit Kerja -->
                  <td class="py-4 px-5 text-on-surface whitespace-nowrap">
                    <span class="font-medium text-xs">
                      {{ $user->bidangStaf->nama_bidang ?? ($user->role === 'admin' ? 'Pusat Kontrol (IT)' : ($user->role === 'front_desk' ? 'Pelayanan Umum' : 'Staf Bidang')) }}
                    </span>
                  </td>

                  <!-- Kolom Peran (Role) -->
                  <td class="py-4 px-5 whitespace-nowrap">
                    @if($user->role === 'admin')
                      <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Admin
                      </span>
                    @elseif($user->role === 'front_desk')
                      <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Front Desk
                      </span>
                    @else
                      <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Staf Bidang
                      </span>
                    @endif
                  </td>

                  <!-- Kolom Tanggal Bergabung -->
                  <td class="py-4 px-5 text-on-surface-variant text-xs whitespace-nowrap">
                    <span class="inline-flex items-center gap-1">
                      <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                      {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                    </span>
                  </td>

                  <!-- Kolom Aksi -->
                  <td class="px-4 py-4 text-right pr-6">
                    <div class="inline-flex items-center gap-1">
                      <!--Tombol Edit-->
                      <button type="button"
                        class="editUserBtn p-2 rounded-lg text-primary hover:bg-blue-50 transition-colors cursor-pointer"
                        title="Edit Data Pengguna" data-user-id="{{ $user->id }}" data-name="{{ $user->name }}"
                        data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                        data-bidang-id="{{ $user->bidang_id }}" data-action="{{ route('admin.users.update', $user->id) }}">
                        <span class="material-symbols-outlined text-[18px] ">edit</span>
                      </button>

                      <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }} ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                          class="p-2 rounded-lg text-error hover:bg-red-50 transition-colors cursor-pointer"
                          title="Hapus Pengguna">
                          <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-12 text-center text-on-surface-variant">
                    <div class="flex flex-col items-center justify-center">
                      <div
                        class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant mb-3">
                        <span class="material-symbols-outlined text-2xl">person_off</span>
                      </div>
                      <span class="text-base font-semibold text-on-surface">Tidak ada pengguna ditemukan</span>
                      <p class="text-xs text-on-surface-variant mt-1 max-w-sm">
                        Belum ada akun pengguna yang terdaftar pada sistem.
                      </p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Table Footer -->
        <div
          class="p-4 bg-surface-container-lowest border-t border-outline-variant/30 flex flex-col sm:flex-row items-center justify-between gap-3 font-label-sm text-label-sm text-on-surface-variant">
          <span>
            Menampilkan <strong class="text-on-surface">{{ $users->count() }}</strong> pengguna terdaftar
          </span>
          <span class="text-xs text-on-surface-variant">
            Status Direktori: <span class="text-emerald-700 font-semibold">Aktif &amp; Terverifikasi</span>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 1: Tambah Pengguna Baru (Add User Modal) -->
  <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4 transition-opacity"
    id="addUserModal">
    <div
      class="bg-surface-container-lowest rounded-xl shadow-xl max-w-lg w-full p-6 border border-outline-variant/40 flex flex-col gap-4 transform transition-all">
      <!-- Header Modal -->
      <div class="flex items-start justify-between">
        <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[22px]">person_add</span>
        </div>
        <button
          class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors cursor-pointer"
          id="closeModalBtn" type="button">
          <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
      </div>
      <div class="flex flex-col gap-1">
        <h3 class="text-lg font-bold font-headline-md text-on-surface">Tambah Pengguna Baru</h3>
        <p class="text-xs font-body-sm text-on-surface-variant">Buat akun pengguna baru dan tetapkan peran akses sistem
        </p>
      </div>

      <!-- Form Modal -->
      <form class="flex flex-col gap-4 mt-1" id="addUserForm" action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="flex flex-col gap-1.5">
          <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="name">
            Nama Lengkap <span class="text-error">*</span>
          </label>
          <input
            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
            id="name" name="name" placeholder="Contoh: Robert Tanaka" required type="text" />
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="email">
            Alamat Email Resmi <span class="text-error">*</span>
          </label>
          <input
            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
            id="email" name="email" placeholder="Contoh: robert@pintas.com" required type="email" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="password">
              Password <span class="text-error">*</span>
            </label>
            <input
              class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
              id="password" name="password" placeholder="Min. 8 karakter" required type="password" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="role">
              Peran Akses <span class="text-error">*</span>
            </label>
            <select
              class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
              id="role" name="role">
              <option value="front_desk">Front Desk</option>
              <option value="staf_bidang">Staf Bidang</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
        </div>

        <!-- Dropdown Bidang (Muncul dinamis jika role staf_bidang) -->
        <div class="flex flex-col gap-1.5 hidden" id="addBidangContainer">
          <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="role_bidang_id">
            Bidang Penempatan <span class="text-xs font-normal text-on-surface-variant">(Khusus Staf Bidang)</span>
          </label>
          <select
            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
            id="role_bidang_id" name="bidang_id">
            <option value="">-- Pilih Bidang Penempatan --</option>
            @foreach($bidangList as $b)
              <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
            @endforeach
          </select>
        </div>

        <!-- Footer Tombol Modal -->
        <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-outline-variant/30">
          <button
            class="px-4 py-2 rounded-lg bg-surface-container border border-outline-variant/50 text-on-surface hover:bg-surface-container-high transition-colors font-medium text-xs cursor-pointer"
            id="cancelModalBtn" type="button">
            Batal
          </button>
          <button
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary hover:bg-[#172554] text-white font-headline-sm text-xs font-semibold transition-colors shadow-sm cursor-pointer"
            type="submit">
            <span class="material-symbols-outlined text-[16px]">save</span>
            <span>Simpan Pengguna</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal 2: Edit Pengguna (Edit User Modal) -->
  <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4 transition-opacity"
    id="editUserModal">
    <div
      class="bg-surface-container-lowest rounded-xl shadow-xl max-w-lg w-full p-6 border border-outline-variant/40 flex flex-col gap-4 transform transition-all">
      <!-- Header Modal -->
      <div class="flex items-start justify-between">
        <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[22px]">edit</span>
        </div>
        <button
          class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors cursor-pointer"
          id="closeEditModalBtn" type="button">
          <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
      </div>
      <div class="flex flex-col gap-1">
        <h3 class="text-lg font-bold font-headline-md text-on-surface">Perbarui Data Pengguna</h3>
        <p class="text-xs font-body-sm text-on-surface-variant">Perbarui data nama, email, password, dan hak akses
          pengguna</p>
      </div>

      <!-- Form Modal -->
      <form class="flex flex-col gap-4 mt-1" id="editUserForm" action="" method="POST">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-1.5">
          <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="edit_name">
            Nama Lengkap <span class="text-error">*</span>
          </label>
          <input
            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
            id="edit_name" name="name" placeholder="Contoh: Robert Tanaka" required type="text" />
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="edit_email">
            Alamat Email Resmi <span class="text-error">*</span>
          </label>
          <input
            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
            id="edit_email" name="email" placeholder="Contoh: robert@pintas.com" required type="email" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="flex flex-col gap-1.5">
            <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="edit_password">
              Password <span class="text-xs font-normal text-on-surface-variant">(Opsional)</span>
            </label>
            <input
              class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
              id="edit_password" name="password" placeholder="Kosongkan jika tetap" type="password" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="edit_role">
              Peran Akses <span class="text-error">*</span>
            </label>
            <select
              class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
              id="edit_role" name="role" required>
              <option value="front_desk">Front Desk</option>
              <option value="staf_bidang">Staf Bidang</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
        </div>

        <!-- Dropdown Bidang Edit (Muncul dinamis jika role staf_bidang) -->
        <div class="flex flex-col gap-1.5 hidden" id="editBidangContainer">
          <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="edit_bidang_id">
            Bidang Penempatan <span class="text-xs font-normal text-on-surface-variant">(Khusus Staf Bidang)</span>
          </label>
          <select
            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
            id="edit_bidang_id" name="bidang_id">
            <option value="">-- Pilih Bidang Penempatan --</option>
            @foreach($bidangList as $b)
              <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
            @endforeach
          </select>
        </div>

        <!-- Footer Tombol Modal -->
        <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-outline-variant/30">
          <button
            class="px-4 py-2 rounded-lg bg-surface-container border border-outline-variant/50 text-on-surface hover:bg-surface-container-high transition-colors font-medium text-xs cursor-pointer"
            id="cancelEditModalBtn" type="button">
            Batal
          </button>
          <button
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary hover:bg-[#172554] text-white font-headline-sm text-xs font-semibold transition-colors shadow-sm cursor-pointer"
            type="submit">
            <span class="material-symbols-outlined text-[16px]">check</span>
            <span>Perbarui Pengguna</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Filter table rows by search text
    const searchInput = document.getElementById('tableSearchInput');
    const rows = document.querySelectorAll('.user-row');

    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(query) ? '' : 'none';
        });
      });
    }

    // Modal open & close interactivity for Add User
    const addUserBtn = document.getElementById('addUserBtn');
    const modal = document.getElementById('addUserModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const addUserForm = document.getElementById('addUserForm');
    const addRole = document.getElementById('role');
    const addBidangContainer = document.getElementById('addBidangContainer');
    const addBidang = document.getElementById('role_bidang_id');

    const toggleAddBidang = () => {
      if (!addRole || !addBidangContainer) return;
      if (addRole.value === 'staf_bidang') {
        addBidangContainer.classList.remove('hidden');
      } else {
        addBidangContainer.classList.add('hidden');
        if (addBidang) addBidang.value = '';
      }
    };

    if (addRole) addRole.addEventListener('change', toggleAddBidang);

    const openModal = () => {
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        toggleAddBidang();
      }
    };

    const closeModal = () => {
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    };

    if (addUserBtn) addUserBtn.addEventListener('click', openModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);

    // Edit User Modal interactivity
    const editModal = document.getElementById('editUserModal');
    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    const cancelEditModalBtn = document.getElementById('cancelEditModalBtn');
    const editUserForm = document.getElementById('editUserForm');
    const editName = document.getElementById('edit_name');
    const editEmail = document.getElementById('edit_email');
    const editRole = document.getElementById('edit_role');
    const editPassword = document.getElementById('edit_password');
    const editBidangContainer = document.getElementById('editBidangContainer');
    const editBidang = document.getElementById('edit_bidang_id');

    const toggleEditBidang = () => {
      if (!editRole || !editBidangContainer) return;
      if (editRole.value === 'staf_bidang') {
        editBidangContainer.classList.remove('hidden');
      } else {
        editBidangContainer.classList.add('hidden');
        if (editBidang) editBidang.value = '';
      }
    };

    if (editRole) editRole.addEventListener('change', toggleEditBidang);

    const openEditModal = (btn) => {
      if (!editModal) return;
      const name = btn.dataset.name || '';
      const email = btn.dataset.email || '';
      const role = btn.dataset.role || 'front_desk';
      const action = btn.dataset.action || '';
      const bidangId = btn.dataset.bidangId || '';

      if (editUserForm) editUserForm.action = action;
      if (editName) editName.value = name;
      if (editEmail) editEmail.value = email;
      if (editRole) editRole.value = role;
      if (editPassword) editPassword.value = '';
      if (editBidang) editBidang.value = bidangId;

      toggleEditBidang();

      editModal.classList.remove('hidden');
      editModal.classList.add('flex');
    };

    const closeEditModal = () => {
      if (!editModal) return;
      editModal.classList.add('hidden');
      editModal.classList.remove('flex');
    };

    document.addEventListener('click', (e) => {
      const editBtn = e.target.closest('.editUserBtn');
      if (editBtn) {
        openEditModal(editBtn);
      }
    });

    if (closeEditModalBtn) closeEditModalBtn.addEventListener('click', closeEditModal);
    if (cancelEditModalBtn) cancelEditModalBtn.addEventListener('click', closeEditModal);

    // Close modals when clicking backdrop outside
    window.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
      if (e.target === editModal) closeEditModal();
    });
  </script>
@endsection
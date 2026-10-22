@extends('layouts.admin')
@section('title', 'Manajemen User')
@section('content')
<div class="flex flex-col w-full px-margin_container py-stack_lg space-y-gutter">
  <!-- Header & Action Ribbon -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-stack_md">
    <div class="flex flex-col space-y-1">
      <div class="flex items-center gap-2">
        <span
          class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-label-sm text-label-sm font-semibold tracking-wider uppercase">
          <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
          Administrasi Sistem • Akses &amp; Otorisasi
        </span>
      </div>
      <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">User Management</h1>
      <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
        Kelola data pengguna sistem, penetapan hak akses, dan distribusi staf helpdesk ICT National Treasury.
      </p>
    </div>
    <div class="flex items-center gap-3">
      <button
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-headline-sm text-headline-sm font-semibold shadow-md hover:bg-primary/90 transition-all active:scale-[0.98]"
        id="addUserBtn" type="button">
        <span class="material-symbols-outlined text-[20px]">person_add</span>
        + Add New User
      </button>
    </div>
  </div>
  <!-- Metric Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-gutter"
    style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
    <!-- Card 1: Total Users -->
    <div
      class="bg-surface-container-lowest rounded-xl p-stack_lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
      <div
        class="absolute -right-4 -top-4 w-20 h-20 bg-primary/5 rounded-full blur-xl group-hover:bg-primary/10 transition-colors">
      </div>
      <div class="flex items-center justify-between">
        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Total Users</span>
        <span class="p-2 rounded-lg bg-surface-container-low text-primary">
          <span class="material-symbols-outlined text-[20px]">group</span>
        </span>
      </div>
      <div class="mt-4 flex items-baseline gap-2">
        <span class="font-headline-lg text-headline-lg font-bold text-on-surface text-[32px] leading-none">{{
          $users->count() }}</span>
      </div>
    </div>
    <!-- Card 2: Active Users -->
    <!-- Card 3: Administrators -->
    <div
      class="bg-surface-container-lowest rounded-xl p-stack_lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
      <div
        class="absolute -right-4 -top-4 w-20 h-20 bg-error-container/40 rounded-full blur-xl group-hover:bg-error-container/60 transition-colors">
      </div>
      <div class="flex items-center justify-between">
        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Administrators</span>
        <span class="p-2 rounded-lg bg-error-container text-on-error-container">
          <span class="material-symbols-outlined text-[20px]">security</span>
        </span>
      </div>
      <div class="mt-4 flex items-baseline gap-2">
        <span class="font-headline-lg text-headline-lg font-bold text-on-surface text-[32px] leading-none">1</span>
      </div>
    </div>
    <!-- Card 4: Technicians -->
    <div
      class="bg-surface-container-lowest rounded-xl p-stack_lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
      <div
        class="absolute -right-4 -top-4 w-20 h-20 bg-secondary-container rounded-full blur-xl group-hover:bg-secondary-fixed transition-colors">
      </div>
      <div class="flex items-center justify-between">
        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Technicians</span>
        <span class="p-2 rounded-lg bg-secondary-container text-on-secondary-fixed-variant">
          <span class="material-symbols-outlined text-[20px]">support_agent</span>
        </span>
      </div>
      <div class="mt-4 flex items-baseline gap-2">
        <span class="font-headline-lg text-headline-lg font-bold text-on-surface text-[32px] leading-none">2</span>
      </div>
    </div>
  </div>
  <!-- Main Table Card: All Users -->
  <div class="bg-surface-container-lowest rounded-xl shadow-sm flex flex-col overflow-hidden">
    <!-- Table Toolbar Header -->
    <div class="p-stack_lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-stack_md">
      <div class="flex items-center gap-stack_sm">
        <h2 class="font-headline-md text-headline-md font-bold text-on-surface">All Users</h2>
        <span
          class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-semibold">{{
          $users->count() }}
          Total</span>
      </div>
      <div class="flex items-center gap-3">
        <!-- Search input inside Table container -->
        <div class="relative w-64 sm:w-80">
          <span
            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
          <input
            class="w-full pl-9 pr-stack_sm py-1.5 rounded-lg bg-surface-container-low text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container text-body-sm font-body-sm transition-colors"
            id="tableSearchInput" placeholder="Search users..." type="text" />
        </div>
        <button
          class="p-2 rounded-lg bg-surface-container-low text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors"
          title="Filter list" type="button">
          <span class="material-symbols-outlined text-[20px]">filter_list</span>
        </button>
      </div>
    </div>
    <!-- Data Table -->
    <div class="w-full overflow-x-auto">
      <table class="w-full text-left" id="usersTable">
        <thead
          class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
          <tr>
            <th class="py-3 px-stack_lg font-semibold" scope="col">User</th>
            <th class="py-3 px-stack_md font-semibold" scope="col">Department</th>
            <th class="py-3 px-stack_md font-semibold" scope="col">Role</th>
            <th class="py-3 px-stack_md font-semibold" scope="col">Tickets Raised</th>

            <th class="py-3 px-stack_md font-semibold" scope="col">Joined</th>
            <th class="py-3 px-stack_lg text-right font-semibold" scope="col">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y-0 text-body-sm font-body-sm text-on-surface">
          @foreach($users as $user)
          <tr
            class="hover:bg-surface-container-low/70 transition-colors user-row {{ $user->role === 'admin' ? 'bg-primary-fixed/15' : '' }}">
            <td class="py-3.5 px-stack_lg">
              <div class="flex items-center gap-3">
                <div
                  class="w-9 h-9 rounded-full bg-primary-fixed text-primary font-headline-sm text-headline-sm font-bold flex items-center justify-center shrink-0">
                  {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="flex flex-col min-w-0">
                  <span class="font-headline-sm text-headline-sm font-semibold text-on-surface truncate">{{ $user->name
                    }}</span>
                  <span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1 truncate">
                    <span class="material-symbols-outlined text-[14px]">mail</span>
                    {{ $user->email }}
                  </span>
                </div>
              </div>
            </td>
            <td class="py-3.5 px-stack_md font-medium text-on-surface">
              {{ $user->role === 'admin' ? 'IT' : 'Operations' }}
            </td>
            <td class="py-3.5 px-stack_md">
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full {{ $user->role === 'admin' ? 'bg-primary text-on-primary font-bold' : 'bg-surface-container-highest text-on-surface-variant font-medium' }} font-label-sm text-label-sm uppercase tracking-wider">
                {{ $user->role }}
              </span>
            </td>
            <td class="py-3.5 px-stack_md font-semibold text-on-surface">
              0
            </td>

            <td class="py-3.5 px-stack_md text-on-surface-variant font-medium">
              <span class="inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                {{ $user->created_at ? $user->created_at->format('n/j/Y') : '-' }}
              </span>
            </td>
            <td class="py-3.5 px-stack_lg text-right relative">
              <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}"
                  class="p-1 rounded-lg text-primary hover:bg-primary-container transition-colors" title="Edit">
                  <span class="material-symbols-outlined text-[20px]">edit</span>
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="p-1 rounded-lg text-error hover:bg-error-container transition-colors"
                    title="Delete">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- Table Footer / Pagination -->
    <div
      class="py-3.5 px-stack_lg bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-3 text-body-sm font-body-sm text-on-surface-variant">
      <div class="flex items-center gap-2">
        <span class="">Menampilkan <strong class="font-semibold text-on-surface">6</strong> pengguna terdaftar</span>
        <span class="text-outline-variant">•</span>
        <span class="">Directory Level: Production</span>
      </div>
      <div class="flex items-center gap-1">
        <button
          class="px-2.5 py-1 rounded bg-surface-container-highest text-on-surface-variant font-medium disabled:opacity-50"
          disabled="" type="button">
          Previous
        </button>
        <button class="px-3 py-1 rounded bg-primary text-on-primary font-semibold text-label-sm" type="button">
          1
        </button>
        <button
          class="px-2.5 py-1 rounded bg-surface-container-highest text-on-surface-variant font-medium disabled:opacity-50"
          disabled="" type="button">
          Next
        </button>
      </div>
    </div>
  </div>
  <!-- Role Governance & Quick Allocation Matrix (Bento section) -->
  <!-- Add User Modal (Hidden by default, interactive) -->
  <div class="fixed inset-0 z-50 bg-inverse-surface/40 backdrop-blur-sm hidden items-center justify-center p-4"
    id="addUserModal">
    <div
      class="bg-surface-container-lowest rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
      <div class="p-stack_lg bg-surface-container-low flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span
            class="p-2 rounded-lg bg-primary text-on-primary material-symbols-outlined text-[20px]">person_add</span>
          <div>
            <h3 class="font-headline-md text-headline-md font-bold text-on-surface">Add New User</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Create user account &amp; assign ministry
              permission</p>
          </div>
        </div>
        <button class="p-1 rounded-lg text-on-surface-variant hover:text-on-surface transition-colors"
          id="closeModalBtn" type="button">
          <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
      </div>
      <form class="p-stack_lg flex flex-col space-y-4" id="addUserForm" action="{{ route('admin.users.store') }}"
        method="POST">
        @csrf
        <div>
          <label class="block font-label-md text-label-md font-semibold text-on-surface mb-1" for="name">Full
            Name</label>
          <input
            class="w-full px-3 py-2 rounded-lg bg-surface-container-low text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container-lowest text-body-sm font-body-sm"
            id="name" name="name" placeholder="e.g. Robert Tanaka" required="" type="text" />
        </div>
        <div>
          <label class="block font-label-md text-label-md font-semibold text-on-surface mb-1" for="email">Official Email
            (@treasury.gov)</label>
          <input
            class="w-full px-3 py-2 rounded-lg bg-surface-container-low text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container-lowest text-body-sm font-body-sm"
            id="email" name="email" placeholder="e.g. robert.tanaka@treasury.gov" required="" type="email" />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-label-md text-label-md font-semibold text-on-surface mb-1"
              for="password">Password</label>
            <input
              class="w-full px-3 py-2 rounded-lg bg-surface-container-low text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container-lowest text-body-sm font-body-sm"
              id="password" name="password" placeholder="Min 8 characters" required="" type="password" />
          </div>
          <div>
            <label class="block font-label-md text-label-md font-semibold text-on-surface mb-1" for="role">Role</label>
            <select
              class="w-full px-3 py-2 rounded-lg bg-surface-container-low text-on-surface focus:outline-none focus:bg-surface-container-lowest text-body-sm font-body-sm"
              id="role" name="role">
              <option value="front_desk">Front Desk</option>
              <option value="staf_bidang">Staf Bidang</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
        </div>
        <div class="pt-4 flex items-center justify-end gap-3">
          <button
            class="px-4 py-2 rounded-lg bg-primary text-on-primary font-headline-sm text-headline-sm font-semibold hover:bg-primary-container transition-colors shadow-sm"
            type="submit">
            Save User
          </button>
        </div>
      </form>
    </div>
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

  // Modal open & close interactivity
  const addUserBtn = document.getElementById('addUserBtn');
  const modal = document.getElementById('addUserModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const cancelModalBtn = document.getElementById('cancelModalBtn');
  const addUserForm = document.getElementById('addUserForm');

  const openModal = () => {
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
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

  if (addUserForm) {
    addUserForm.addEventListener('submit', (e) => {
      // Allow the form to submit to Laravel normally
    });
  }


</script>
@endsection
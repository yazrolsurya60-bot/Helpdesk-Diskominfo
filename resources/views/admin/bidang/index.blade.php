@extends('layouts.admin')
@section('title', 'Manajemen Bidang')
@section('content')
  <div class="flex flex-col w-full">
    <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full">
      <!-- 1. Header Title & Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
          <div class="inline-flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-primary">
              ADMINISTRASI SISTEM • DATA MASTER
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg text-on-surface tracking-tight mt-1">
            Manajemen Bidang <span class="sr-only">Bidang Management</span>
          </h1>
          <p class="text-sm font-body-md text-on-surface-variant max-w-2xl">
            Kelola data master bidang kerja dan unit penugasan teknis tiket bantuan helpdesk PINTAS.
          </p>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-auto">
          <button
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-on-primary font-headline-sm text-headline-sm font-semibold shadow-sm hover:bg-[#172554] transition-all active:scale-[0.98] cursor-pointer"
            id="addBidangBtn" type="button">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            <span>Tambah Bidang</span>
          </button>
        </div>
      </div>

      <!-- 2. Metric Summary KPI Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <!-- Card 1: Total Bidang -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
              Total Bidang
            </span>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
              <span class="material-symbols-outlined text-[22px]">domain</span>
            </div>
          </div>
          <div class="mt-4 flex items-baseline gap-2">
            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
              {{ $totalBidang }}
            </span>
            <span class="text-xs font-medium text-on-surface-variant">Bidang Terdaftar</span>
          </div>
        </div>

        <!-- Card 2: Staf Terdistribusi -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
              Staf Terdistribusi
            </span>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
              <span class="material-symbols-outlined text-[22px]">badge</span>
            </div>
          </div>
          <div class="mt-4 flex items-baseline gap-2">
            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
              {{ $totalStafTerdistribusi }}
            </span>
            <span class="text-xs font-medium text-on-surface-variant">Personel Staf Teknis</span>
          </div>
        </div>
      </div>

      <!-- 3. Tabel Data Master Bidang & Filter Toolbar -->
      <div
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 shadow-sm overflow-hidden flex flex-col">
        <!-- Filter Toolbar -->
        <div
          class="p-4 sm:p-5 border-b border-outline-variant/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface-container-low/40">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
              <span class="material-symbols-outlined text-[20px]">list_alt</span>
            </div>
            <h2 class="text-base font-bold font-headline-sm text-on-surface">Daftar Bidang Staf</h2>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-surface-container-high text-on-surface-variant">
              {{ $bidangList->total() }} Data
            </span>
          </div>

          <!-- Search Form -->
          <form method="GET" action="{{ route('admin.bidang.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px]">search</span>
              </span>
              <input type="text" name="search" value="{{ request('search', request('q')) }}"
                placeholder="Cari nama bidang..."
                class="w-full pl-9 pr-4 py-2 bg-surface-container-lowest rounded-lg border border-outline-variant/50 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary placeholder-on-surface-variant/60" />
            </div>
            @if(request('search') || request('q'))
              <a href="{{ route('admin.bidang.index') }}"
                class="p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors"
                title="Reset Pencarian">
                <span class="material-symbols-outlined text-[20px]">close</span>
              </a>
            @endif
            <button type="submit"
              class="hidden sm:inline-flex items-center px-3.5 py-2 rounded-lg bg-surface-container text-on-surface text-sm font-semibold hover:bg-surface-container-high transition-colors">
              Cari
            </button>
          </form>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-low border-b border-outline-variant/30 text-on-surface-variant">
                <th scope="col" class="px-4 py-3.5 text-xs font-bold font-label-md uppercase tracking-wider w-16 text-center">
                  NO
                </th>
                <th scope="col" class="px-4 py-3.5 text-xs font-bold font-label-md uppercase tracking-wider">
                  NAMA BIDANG
                </th>
                <th scope="col" class="px-4 py-3.5 text-xs font-bold font-label-md uppercase tracking-wider">
                  JUMLAH STAF TERDAFTAR
                </th>
                <th scope="col" class="px-4 py-3.5 text-xs font-bold font-label-md uppercase tracking-wider">
                  TANGGAL DIBUAT
                </th>
                <th scope="col" class="px-4 py-3.5 text-xs font-bold font-label-md uppercase tracking-wider text-right pr-6">
                  AKSI
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20 text-sm font-body-sm text-on-surface">
              @forelse($bidangList as $index => $bidang)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                  <td class="px-4 py-4 text-center font-medium text-on-surface-variant">
                    {{ $bidangList->firstItem() + $index }}
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]">folder</span>
                      </div>
                      <span class="font-semibold text-on-surface">{{ $bidang->nama_bidang }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-4">
                    @if($bidang->users_count > 0)
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="material-symbols-outlined text-[14px]">people</span>
                        <span>{{ $bidang->users_count }} Staf</span>
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-surface-container text-on-surface-variant border border-outline-variant/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        <span>Belum Ada Staf</span>
                      </span>
                    @endif
                  </td>
                  <td class="px-4 py-4 text-on-surface-variant">
                    {{ $bidang->created_at ? $bidang->created_at->format('d M Y, H:i') : '-' }}
                  </td>
                  <td class="px-4 py-4 text-right pr-6">
                    <div class="inline-flex items-center gap-1">
                      <!-- Tombol Edit -->
                      <button type="button"
                        data-id="{{ $bidang->id }}"
                        data-nama="{{ $bidang->nama_bidang }}"
                        data-action="{{ route('admin.bidang.update', $bidang->id) }}"
                        class="editBidangBtn p-2 rounded-lg text-primary hover:bg-blue-50 transition-colors cursor-pointer"
                        title="Edit Bidang">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                      </button>

                      <!-- Tombol Hapus -->
                      <form action="{{ route('admin.bidang.destroy', $bidang->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang {{ $bidang->nama_bidang }}?');">
                        @csrf
                        @method('DELETE')
                        @if($bidang->users_count > 0)
                          <button type="button"
                            onclick="alert('Tidak dapat menghapus bidang karena masih memiliki {{ $bidang->users_count }} staf teknis terkait. Alihkan staf ke bidang lain terlebih dahulu.');"
                            class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                            title="Tidak dapat menghapus: masih ada staf terkait">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                          </button>
                        @else
                          <button type="submit"
                            class="p-2 rounded-lg text-error hover:bg-red-50 transition-colors cursor-pointer"
                            title="Hapus Bidang">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                          </button>
                        @endif
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-4 py-12 text-center text-on-surface-variant">
                    <div class="flex flex-col items-center justify-center gap-3">
                      <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-[28px]">search_off</span>
                      </div>
                      <div class="flex flex-col gap-0.5">
                        <span class="font-semibold text-on-surface">Tidak ada data bidang ditemukan</span>
                        <span class="text-xs text-on-surface-variant">
                          @if(request('search') || request('q'))
                            Hasil pencarian untuk kata kunci "{{ request('search', request('q')) }}" tidak ditemukan.
                          @else
                            Belum ada master bidang yang didaftarkan dalam sistem.
                          @endif
                        </span>
                      </div>
                      @if(request('search') || request('q'))
                        <a href="{{ route('admin.bidang.index') }}"
                          class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-surface-container-high text-on-surface hover:bg-surface-container transition-colors">
                          <span class="material-symbols-outlined text-[16px]">refresh</span>
                          <span>Reset Filter</span>
                        </a>
                      @endif
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination Footer -->
        @if($bidangList->hasPages())
          <div class="p-4 border-t border-outline-variant/30 bg-surface-container-low/20">
            {{ $bidangList->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Modal Tambah Bidang (File Terpisah: create.blade.php) -->
  @include('admin.bidang.create')

  <!-- Modal Edit Bidang (File Terpisah: edit.blade.php) -->
  @include('admin.bidang.edit')

  <!-- Interactive JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Add Modal Elements
      const addModal = document.getElementById('addBidangModal');
      const addBtn = document.getElementById('addBidangBtn');
      const closeAddBtn = document.getElementById('closeAddModalBtn');
      const cancelAddBtn = document.getElementById('cancelAddModalBtn');
      const inputNamaBidang = document.getElementById('nama_bidang');

      // Edit Modal Elements
      const editModal = document.getElementById('editBidangModal');
      const editForm = document.getElementById('editBidangForm');
      const editInputNama = document.getElementById('edit_nama_bidang');
      const closeEditBtn = document.getElementById('closeEditModalBtn');
      const cancelEditBtn = document.getElementById('cancelEditModalBtn');

      // Functions to open and close Add Modal
      const openAddModal = () => {
        if (!addModal) return;
        addModal.classList.remove('hidden');
        if (inputNamaBidang) {
          inputNamaBidang.value = '';
          setTimeout(() => inputNamaBidang.focus(), 50);
        }
      };

      const closeAddModal = () => {
        if (!addModal) return;
        addModal.classList.add('hidden');
      };

      // Functions to open and close Edit Modal
      const openEditModal = (btn) => {
        if (!editModal || !editForm || !editInputNama) return;
        const action = btn.dataset.action || '';
        const nama = btn.dataset.nama || '';

        editForm.action = action;
        editInputNama.value = nama;

        editModal.classList.remove('hidden');
        setTimeout(() => editInputNama.focus(), 50);
      };

      const closeEditModal = () => {
        if (!editModal) return;
        editModal.classList.add('hidden');
      };

      // Event listeners for Add Modal
      if (addBtn) addBtn.addEventListener('click', openAddModal);
      if (closeAddBtn) closeAddBtn.addEventListener('click', closeAddModal);
      if (cancelAddBtn) cancelAddBtn.addEventListener('click', closeAddModal);

      // Event listeners for Edit Modal
      document.addEventListener('click', (e) => {
        const btn = e.target.closest('.editBidangBtn');
        if (btn) {
          openEditModal(btn);
        }
      });

      if (closeEditBtn) closeEditBtn.addEventListener('click', closeEditModal);
      if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeEditModal);

      // Backdrop Click to close
      window.addEventListener('click', (e) => {
        if (e.target === addModal) closeAddModal();
        if (e.target === editModal) closeEditModal();
      });

      // Escape key to close
      window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeAddModal();
          closeEditModal();
        }
      });
    });
  </script>
@endsection

<!-- Modal Tambah Bidang -->
<div
  class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden"
  id="addBidangModal">
  <div
    class="bg-surface-container-lowest rounded-2xl border border-outline-variant/40 shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
    <div class="p-5 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-low/30">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
          <span class="material-symbols-outlined text-[22px]">add_circle</span>
        </div>
        <div>
          <h3 class="text-base font-bold font-headline-sm text-on-surface">Tambah Bidang Baru</h3>
          <p class="text-xs text-on-surface-variant font-body-sm">Daftarkan unit bidang master baru ke dalam sistem</p>
        </div>
      </div>
      <button class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg hover:bg-surface-container transition-colors"
        id="closeAddModalBtn" type="button">
        <span class="material-symbols-outlined text-[20px]">close</span>
      </button>
    </div>

    <form action="{{ route('admin.bidang.store') }}" method="POST" class="p-5 flex flex-col gap-4">
      @csrf
      <div class="flex flex-col gap-1.5">
        <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant" for="nama_bidang">
          Nama Bidang <span class="text-error">*</span>
        </label>
        <input
          class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs placeholder-on-surface-variant/50"
          id="nama_bidang" name="nama_bidang" placeholder="Contoh: Aplikasi Informatika & Persandian" required type="text" />
        <span class="text-[11px] text-on-surface-variant">Pastikan nama bidang unik dan belum pernah didaftarkan.</span>
      </div>

      <div class="pt-3 border-t border-outline-variant/20 flex items-center justify-end gap-2.5">
        <button
          class="px-4 py-2 rounded-lg text-sm font-semibold text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer"
          id="cancelAddModalBtn" type="button">
          Batal
        </button>
        <button
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary text-sm font-semibold hover:bg-[#172554] shadow-sm transition-all active:scale-[0.98] cursor-pointer"
          type="submit">
          <span class="material-symbols-outlined text-[18px]">check</span>
          <span>Simpan Bidang</span>
        </button>
      </div>
    </form>
  </div>
</div>

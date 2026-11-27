@extends('layouts.app')

@section('title', 'Ubah Status Perbaikan - ' . $report['nomor_tiket'])

@section('navbar-left')
    <div class="flex items-center gap-space-sm text-on-surface">
        <a href="{{ route('teknisi.kerusakan.index') }}" class="text-on-surface-variant hover:text-primary transition-colors flex items-center">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <span class="text-outline">/</span>
        <span class="font-label-md text-label-md font-semibold text-primary">Ubah Status Perbaikan</span>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto w-full flex flex-col gap-space-lg">

    {{-- Breadcrumb Nav & Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Ubah Status Perbaikan</h1>
                <span class="font-mono text-label-md font-semibold text-secondary bg-surface-container px-2 py-0.5 rounded">
                    {{ $report['nomor_tiket'] }}
                </span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Perbarui progres penanganan laporan kerusakan fasilitas.</p>
        </div>
        <a href="{{ route('teknisi.kerusakan.index') }}" class="inline-flex items-center gap-1.5 px-space-md py-2 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all font-label-md text-label-md self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    {{-- Ringkasan Tiket (Single Clean Card) --}}
    <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-outline-variant/10 text-body-md">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-space-md text-caption-sm">
            <div>
                <span class="text-on-surface-variant block">Fasilitas:</span>
                <strong class="text-primary text-[13px] block mt-0.5">{{ $report['fasilitas_nama'] }}</strong>
            </div>
            <div>
                <span class="text-on-surface-variant block">Lokasi:</span>
                <strong class="text-primary text-[13px] block mt-0.5">{{ $report['gedung_nama'] }}</strong>
                <span class="text-on-surface-variant text-[11px]">{{ $report['kamar_nomor'] }}</span>
            </div>
            <div>
                <span class="text-on-surface-variant block">Pelapor:</span>
                <strong class="text-primary text-[13px] block mt-0.5">{{ $report['pelapor_nama'] }}</strong>
                <span class="text-on-surface-variant text-[11px]">{{ $report['pelapor_telepon'] }}</span>
            </div>
            <div>
                <span class="text-on-surface-variant block">Status Saat Ini:</span>
                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-medium {{ $report['status_badge']['class'] }}">
                    {{ $report['status_badge']['label'] }}
                </span>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-surface-container text-caption-sm text-on-surface-variant">
            <span class="font-medium text-primary">Kendala:</span> {{ $report['deskripsi'] }}
        </div>
    </div>

    {{-- Form Ubah Status --}}
    <form id="form-ubah-status" method="POST" action="{{ route('teknisi.kerusakan.update-status', $report['id_laporan']) }}" enctype="multipart/form-data" class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col gap-space-lg">
        @csrf
        @method('PUT')

        {{-- Pilihan Status --}}
        <div>
            <label class="block font-semibold text-primary text-label-md mb-2">
                Pilih Status Perbaikan Terbaru <span class="text-error">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors">
                    <input type="radio" name="status_perbaikan" value="diajukan" @checked($report['status_perbaikan'] === 'diajukan' || $report['status_perbaikan'] === 'menunggu') class="text-secondary focus:ring-secondary">
                    <div>
                        <strong class="text-primary text-label-md block">Menunggu</strong>
                        <span class="text-caption-sm text-on-surface-variant">Laporan belum dimulai</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-lg border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors">
                    <input type="radio" name="status_perbaikan" value="dalam_pengerjaan" @checked($report['status_perbaikan'] === 'dalam_pengerjaan' || $report['status_perbaikan'] === 'ditugaskan') class="text-secondary focus:ring-secondary">
                    <div>
                        <strong class="text-primary text-label-md block">Diproses (Pengerjaan)</strong>
                        <span class="text-caption-sm text-on-surface-variant">Sedang dalam perbaikan fisik</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-lg border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors">
                    <input type="radio" name="status_perbaikan" value="tertunda" @checked($report['status_perbaikan'] === 'tertunda') class="text-secondary focus:ring-secondary">
                    <div>
                        <strong class="text-primary text-label-md block">Tertunda</strong>
                        <span class="text-caption-sm text-on-surface-variant">Menunggu suku cadang/vendor</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-lg border border-outline-variant/30 hover:bg-surface-container-low cursor-pointer transition-colors">
                    <input type="radio" name="status_perbaikan" value="selesai" @checked($report['status_perbaikan'] === 'selesai') class="text-secondary focus:ring-secondary">
                    <div>
                        <strong class="text-primary text-label-md block">Selesai Diperbaiki</strong>
                        <span class="text-caption-sm text-on-surface-variant">Fasilitas sudah berfungsi normal</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Alasan Penundaan --}}
        <div id="field-alasan-tertunda" class="{{ $report['status_perbaikan'] === 'tertunda' ? '' : 'hidden' }}">
            <label for="alasan_tertunda" class="block font-semibold text-primary text-label-md mb-1">
                Alasan Penundaan <span class="text-error">*</span>
            </label>
            <input type="text" id="alasan_tertunda" name="alasan_tertunda" value="{{ old('alasan_tertunda', $report['catatan_teknisi'] ?? '') }}" placeholder="Contoh: Menunggu suku cadang dari logistik..." class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary/20 border border-outline-variant/20">
        </div>

        {{-- Catatan Lapangan --}}
        <div>
            <label for="catatan" class="block font-semibold text-primary text-label-md mb-1">
                Catatan Pekerjaan &amp; Pemeriksaan Lapangan
            </label>
            <textarea id="catatan" name="catatan" rows="3" placeholder="Tuliskan catatan teknis pemeriksaan atau perbaikan..." class="w-full p-3 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-secondary/20 border border-outline-variant/20 resize-y">{{ old('catatan', $report['catatan_teknisi'] ?? '') }}</textarea>
        </div>

        {{-- Unggah Bukti --}}
        <div>
            <label for="bukti_perbaikan" class="block font-semibold text-primary text-label-md mb-1">
                Unggah Bukti Perbaikan <span class="font-normal text-on-surface-variant text-caption-sm">(Foto/Dokumen, maks. 5 MB)</span>
            </label>
            <input type="file" id="bukti_perbaikan" name="bukti_perbaikan" accept="image/jpeg,image/png,application/pdf" class="w-full text-caption-sm file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-surface-container file:text-primary cursor-pointer p-2 bg-surface-container-low rounded-lg border border-outline-variant/20">
        </div>

        {{-- Form Actions --}}
        <div class="pt-space-md border-t border-surface-container flex items-center justify-between">
            <a href="{{ route('teknisi.kerusakan.index') }}" class="px-4 py-2 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors">
                Batal
            </a>
            <button type="button" onclick="openConfirmationModal()" class="px-5 py-2 rounded-lg bg-secondary hover:bg-on-secondary-fixed-variant text-on-secondary font-label-md text-label-md font-semibold shadow-sm cursor-pointer">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Dialog Modal Konfirmasi Perubahan Status --}}
    <dialog id="modal-konfirmasi-status" class="w-[min(480px,calc(100%-2rem))] m-auto rounded-xl p-0 border-0 bg-white shadow-xl overflow-hidden backdrop:bg-primary-container/40">
        <div class="flex flex-col">
            <div class="px-space-md py-3 border-b border-surface-container flex items-center justify-between bg-surface-container-lowest">
                <h2 class="font-title-md text-title-md text-primary font-bold">Konfirmasi Perubahan Status</h2>
                <button type="button" onclick="document.getElementById('modal-konfirmasi-status').close()" class="p-1 rounded text-on-surface-variant hover:bg-surface-container cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="p-space-md space-y-3 text-caption-sm">
                <p class="text-on-surface-variant">Simpan pembaruan status laporan berikut?</p>

                <div class="p-3 bg-surface-container-low rounded-lg space-y-1">
                    <div class="flex justify-between">
                        <span class="text-outline">Tiket:</span>
                        <strong class="font-mono text-primary">{{ $report['nomor_tiket'] }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-outline">Fasilitas:</span>
                        <span class="font-medium text-primary">{{ $report['fasilitas_nama'] }}</span>
                    </div>
                </div>

                <div class="p-3 rounded-lg border border-surface-container flex items-center justify-around text-center">
                    <div>
                        <span class="text-[11px] text-outline block">Status Awal</span>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-medium {{ $report['status_badge']['class'] }}">
                            {{ $report['status_badge']['label'] }}
                        </span>
                    </div>
                    <span class="material-symbols-outlined text-outline text-[20px]">arrow_forward</span>
                    <div>
                        <span class="text-[11px] text-outline block">Status Baru</span>
                        <span id="confirm-new-status-badge" class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-secondary text-on-secondary">
                        </span>
                    </div>
                </div>

                <div id="confirm-alasan-box" hidden class="p-2.5 bg-[#fff3df] rounded text-caption-sm text-[#99620a]">
                    <strong>Alasan:</strong> <span id="confirm-alasan-text"></span>
                </div>
            </div>

            <div class="px-space-md py-3 border-t border-surface-container bg-surface-container-lowest flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-konfirmasi-status').close()" class="px-3.5 py-1.5 rounded bg-surface-container-low hover:bg-surface-container text-on-surface text-caption-sm cursor-pointer">
                    Periksa Kembali
                </button>
                <button type="button" onclick="submitStatusForm()" class="px-4 py-1.5 rounded bg-secondary hover:bg-on-secondary-fixed-variant text-on-secondary text-caption-sm font-semibold cursor-pointer shadow-xs">
                    Ya, Simpan Perubahan
                </button>
            </div>
        </div>
    </dialog>

</div>

@push('scripts')
<script>
    const radioInputs = document.querySelectorAll('input[name="status_perbaikan"]');
    const fieldAlasan = document.getElementById('field-alasan-tertunda');
    const inputAlasan = document.getElementById('alasan_tertunda');
    const fileInput = document.getElementById('bukti_perbaikan');
    const modalKonfirmasi = document.getElementById('modal-konfirmasi-status');
    const statusForm = document.getElementById('form-ubah-status');

    radioInputs.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'tertunda') {
                fieldAlasan.classList.remove('hidden');
                inputAlasan.setAttribute('required', 'required');
                inputAlasan.focus();
            } else {
                fieldAlasan.classList.add('hidden');
                inputAlasan.removeAttribute('required');
            }
        });
    });

    function getSelectedStatusLabel(val) {
        switch(val) {
            case 'diajukan':
            case 'menunggu':
                return { label: 'Menunggu', class: 'bg-surface-container text-secondary' };
            case 'dalam_pengerjaan':
            case 'diproses':
                return { label: 'Diproses (Pengerjaan)', class: 'bg-[#fff3df] text-[#99620a]' };
            case 'tertunda':
                return { label: 'Tertunda', class: 'bg-error-container text-error' };
            case 'selesai':
                return { label: 'Selesai Diperbaiki', class: 'bg-success-container text-success' };
            default:
                return { label: val, class: 'bg-surface-container text-on-surface' };
        }
    }

    function openConfirmationModal() {
        const selectedRadio = document.querySelector('input[name="status_perbaikan"]:checked');
        if (!selectedRadio) {
            alert('Silakan pilih salah satu status perbaikan.');
            return;
        }

        if (selectedRadio.value === 'tertunda' && !inputAlasan.value.trim()) {
            alert('Alasan penundaan wajib diisi jika status dipilih Tertunda.');
            inputAlasan.focus();
            return;
        }

        const statusInfo = getSelectedStatusLabel(selectedRadio.value);
        const confirmBadge = document.getElementById('confirm-new-status-badge');
        confirmBadge.textContent = statusInfo.label;
        confirmBadge.className = 'inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-semibold ' + statusInfo.class;

        const confirmAlasanBox = document.getElementById('confirm-alasan-box');
        if (selectedRadio.value === 'tertunda') {
            confirmAlasanBox.hidden = false;
            document.getElementById('confirm-alasan-text').textContent = inputAlasan.value.trim();
        } else {
            confirmAlasanBox.hidden = true;
        }

        modalKonfirmasi.showModal();
    }

    function submitStatusForm() {
        modalKonfirmasi.close();
        statusForm.submit();
    }
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Form Pengecekan Kamar')

@section('navbar-left')
    <span class="material-symbols-outlined">checklist</span>
    <span class="font-title-md">Form Pengecekan</span>
@endsection

@section('content')
<div class="bg-surface-container-lowest min-h-screen" style="padding: var(--space-lg);">

    {{-- Breadcrumb & Header --}}
    <div style="display: flex; align-items: center; gap: var(--space-sm); margin-bottom: var(--space-md);">
        <a href="{{ route('go.pengecekan-kamar.index') }}" class="text-on-surface-variant" style="display: inline-flex; align-items: center; text-decoration: none;">
            <span class="material-symbols-outlined" style="font-size: 22px;">arrow_back</span>
        </a>
        <span class="font-body-md text-on-surface-variant">Pengecekan Kamar</span>
        <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 18px;">chevron_right</span>
        <span class="font-body-md text-primary">Form Pengecekan</span>
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--space-md); margin-bottom: var(--space-xl);">
        <div>
            <h1 class="font-headline-lg text-primary" style="margin-bottom: var(--space-xs);">Audit #{{ $nomorAudit }}</h1>
            <div style="display: flex; align-items: center; gap: var(--space-sm);">
                <span class="font-label-md" style="background: var(--info-container); color: var(--on-info-container); padding: 4px 12px; border-radius: 999px;">Sedang Diperiksa</span>
                <span class="font-caption-sm text-on-surface-variant" id="timer">00:00:00</span>
            </div>
        </div>
    </div>

    <form action="{{ route('go.pengecekan-kamar.selesai', $checkout->id_pengajuan) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Identity Card --}}
        <div class="bg-primary-container" style="border-radius: 16px; padding: var(--space-lg); margin-bottom: var(--space-xl);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-lg);">
                <div>
                    <span class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Mahasiswa</span>
                    <div style="display: flex; align-items: center; gap: var(--space-sm);">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center;">
                            <span class="font-label-md text-surface-container-lowest">{{ strtoupper(substr($pengguna->nama_lengkap ?? '-', 0, 1)) }}</span>
                        </div>
                        <div>
                            <span class="font-body-md text-primary" style="display: block;">{{ $pengguna->nama_lengkap ?? '-' }}</span>
                            <span class="font-caption-sm text-on-surface-variant">{{ $pengguna->email ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Alokasi Kamar</span>
                    <span class="font-body-md text-primary" style="display: block;">{{ $gedung->nama_gedung ?? '-' }} &mdash; Kamar {{ $kamar->nomor_kamar ?? '-' }}</span>
                </div>

                <div>
                    <span class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Tanggal Check-out</span>
                    <span class="font-body-md text-primary" style="display: block;">{{ \Carbon\Carbon::parse($checkout->tanggal_checkout)->format('d M Y') }}</span>
                </div>

                <div>
                    <span class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Petugas</span>
                    <span class="font-body-md text-primary" style="display: block;">{{ $petugas->nama_lengkap ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Section A: Kondisi Fisik --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); margin-bottom: var(--space-xl); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant); display: flex; align-items: center; gap: var(--space-sm);">
                <span class="material-symbols-outlined text-primary" style="font-size: 24px;">home_repair_service</span>
                <h2 class="font-title-md text-primary">Bagian A — Kondisi Fisik</h2>
            </div>

            <div style="padding: var(--space-lg);">
                @foreach ($checklistA as $index => $item)
                    <div style="padding: var(--space-md) 0; {{ !$loop->last ? 'border-bottom: 1px solid var(--outline-variant);' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-md);">
                            <div style="flex: 1; min-width: 200px;">
                                <span class="font-body-md text-primary" style="display: block; margin-bottom: var(--space-xs);">{{ $item['nama'] }}</span>
                                <span class="font-caption-sm text-on-surface-variant">{{ $item['deskripsi'] ?? '' }}</span>
                            </div>
                            <div style="display: flex; gap: var(--space-md); align-items: center;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_a[{{ $index }}][kondisi]" value="baik" required>
                                    <span class="font-label-md text-primary">Baik</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_a[{{ $index }}][kondisi]" value="rusak_ringan">
                                    <span class="font-label-md text-primary">Rusak Ringan</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_a[{{ $index }}][kondisi]" value="rusak_berat">
                                    <span class="font-label-md text-primary">Rusak Berat</span>
                                </label>
                            </div>
                        </div>
                        <textarea name="checklist_a[{{ $index }}][catatan]" placeholder="Catatan tambahan (opsional)..." class="font-body-md text-primary" style="width: 100%; margin-top: var(--space-sm); padding: 8px 12px; border-radius: 10px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); resize: vertical; min-height: 40px;" rows="1"></textarea>
                        <input type="hidden" name="checklist_a[{{ $index }}][item_id]" value="{{ $item['id'] }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section B: Inventaris Perabot --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); margin-bottom: var(--space-xl); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant); display: flex; align-items: center; gap: var(--space-sm);">
                <span class="material-symbols-outlined text-primary" style="font-size: 24px;">chair</span>
                <h2 class="font-title-md text-primary">Bagian B — Inventaris Perabot</h2>
            </div>

            <div style="padding: var(--space-lg);">
                @foreach ($checklistB as $index => $item)
                    <div style="padding: var(--space-md) 0; {{ !$loop->last ? 'border-bottom: 1px solid var(--outline-variant);' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-md);">
                            <div style="flex: 1; min-width: 200px;">
                                <span class="font-body-md text-primary" style="display: block; margin-bottom: var(--space-xs);">{{ $item['nama'] }}</span>
                                <span class="font-caption-sm text-on-surface-variant">{{ $item['deskripsi'] ?? '' }}</span>
                            </div>
                            <div style="display: flex; gap: var(--space-md); align-items: center;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_b[{{ $index }}][kondisi]" value="baik" required>
                                    <span class="font-label-md text-primary">Baik</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_b[{{ $index }}][kondisi]" value="rusak_ringan">
                                    <span class="font-label-md text-primary">Rusak Ringan</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_b[{{ $index }}][kondisi]" value="rusak_berat">
                                    <span class="font-label-md text-primary">Rusak Berat</span>
                                </label>
                            </div>
                        </div>
                        <textarea name="checklist_b[{{ $index }}][catatan]" placeholder="Catatan tambahan (opsional)..." class="font-body-md text-primary" style="width: 100%; margin-top: var(--space-sm); padding: 8px 12px; border-radius: 10px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); resize: vertical; min-height: 40px;" rows="1"></textarea>
                        <input type="hidden" name="checklist_b[{{ $index }}][item_id]" value="{{ $item['id'] }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section C: Kelistrikan & Sanitasi --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); margin-bottom: var(--space-xl); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant); display: flex; align-items: center; gap: var(--space-sm);">
                <span class="material-symbols-outlined text-primary" style="font-size: 24px;">electrical_services</span>
                <h2 class="font-title-md text-primary">Bagian C — Kelistrikan & Sanitasi</h2>
            </div>

            <div style="padding: var(--space-lg);">
                @foreach ($checklistC as $index => $item)
                    <div style="padding: var(--space-md) 0; {{ !$loop->last ? 'border-bottom: 1px solid var(--outline-variant);' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-md);">
                            <div style="flex: 1; min-width: 200px;">
                                <span class="font-body-md text-primary" style="display: block; margin-bottom: var(--space-xs);">{{ $item['nama'] }}</span>
                                <span class="font-caption-sm text-on-surface-variant">{{ $item['deskripsi'] ?? '' }}</span>
                            </div>
                            <div style="display: flex; gap: var(--space-md); align-items: center;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_c[{{ $index }}][kondisi]" value="baik" required>
                                    <span class="font-label-md text-primary">Baik</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_c[{{ $index }}][kondisi]" value="rusak_ringan">
                                    <span class="font-label-md text-primary">Rusak Ringan</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                    <input type="radio" name="checklist_c[{{ $index }}][kondisi]" value="rusak_berat">
                                    <span class="font-label-md text-primary">Rusak Berat</span>
                                </label>
                            </div>
                        </div>
                        <textarea name="checklist_c[{{ $index }}][catatan]" placeholder="Catatan tambahan (opsional)..." class="font-body-md text-primary" style="width: 100%; margin-top: var(--space-sm); padding: 8px 12px; border-radius: 10px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); resize: vertical; min-height: 40px;" rows="1"></textarea>
                        <input type="hidden" name="checklist_c[{{ $index }}][item_id]" value="{{ $item['id'] }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- RFID & Kunci Verification --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); margin-bottom: var(--space-xl); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant); display: flex; align-items: center; gap: var(--space-sm);">
                <span class="material-symbols-outlined text-primary" style="font-size: 24px;">contactless</span>
                <h2 class="font-title-md text-primary">Verifikasi RFID & Kunci</h2>
            </div>

            <div style="padding: var(--space-lg); display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-xl);">
                <div>
                    <span class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-sm);">Status RFID</span>
                    <div style="display: flex; gap: var(--space-lg);">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="rfid_status" value="valid" required>
                            <span class="font-body-md text-primary">Valid</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="rfid_status" value="tidak_valid">
                            <span class="font-body-md text-primary">Tidak Valid</span>
                        </label>
                    </div>
                </div>

                <div>
                    <span class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-sm);">Kunci Fisik</span>
                    <div style="display: flex; gap: var(--space-lg);">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="kunci_fisik" value="dikembalikan" required>
                            <span class="font-body-md text-primary">Dikembalikan</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="kunci_fisik" value="belum">
                            <span class="font-body-md text-primary">Belum</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Photo Documentation --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); margin-bottom: var(--space-xl); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant); display: flex; align-items: center; gap: var(--space-sm);">
                <span class="material-symbols-outlined text-primary" style="font-size: 24px;">photo_camera</span>
                <h2 class="font-title-md text-primary">Dokumentasi Foto</h2>
            </div>

            <div style="padding: var(--space-lg);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-md);">
                    @for ($i = 1; $i <= 4; $i++)
                        <div style="border: 2px dashed var(--outline-variant); border-radius: 12px; padding: var(--space-lg); text-align: center; position: relative;">
                            <input type="file" name="foto_{{ $i }}" accept="image/*" id="foto_{{ $i }}" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 1;">
                            <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 36px; display: block; margin-bottom: var(--space-xs);">add_a_photo</span>
                            <span class="font-caption-sm text-on-surface-variant">Foto {{ $i }}</span>
                            <p class="font-caption-sm text-on-surface-variant" style="margin-top: var(--space-xs);">Klik atau seret file</p>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Summary Metrics --}}
        <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); margin-bottom: var(--space-xl); overflow: hidden;">
            <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant); display: flex; align-items: center; gap: var(--space-sm);">
                <span class="material-symbols-outlined text-primary" style="font-size: 24px;">analytics</span>
                <h2 class="font-title-md text-primary">Ringkasan Pemeriksaan</h2>
            </div>

            <div style="padding: var(--space-lg); display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: var(--space-md);">
                <div style="text-align: center; padding: var(--space-md);">
                    <span class="font-headline-lg text-primary" id="count-baik">0</span>
                    <span class="font-caption-sm text-on-surface-variant" style="display: block; margin-top: var(--space-xs);">Item Baik</span>
                </div>
                <div style="text-align: center; padding: var(--space-md);">
                    <span class="font-headline-lg" style="color: var(--warning);" id="count-rusak-ringan">0</span>
                    <span class="font-caption-sm text-on-surface-variant" style="display: block; margin-top: var(--space-xs);">Rusak Ringan</span>
                </div>
                <div style="text-align: center; padding: var(--space-md);">
                    <span class="font-headline-lg" style="color: var(--error);" id="count-rusak-berat">0</span>
                    <span class="font-caption-sm text-on-surface-variant" style="display: block; margin-top: var(--space-xs);">Rusak Berat</span>
                </div>
                <div style="text-align: center; padding: var(--space-md);">
                    <span class="font-headline-lg text-primary" id="count-total">0</span>
                    <span class="font-caption-sm text-on-surface-variant" style="display: block; margin-top: var(--space-xs);">Total Diperiksa</span>
                </div>
            </div>
        </div>

        {{-- Authority Notice --}}
        <div class="bg-primary-container" style="border-radius: 16px; padding: var(--space-lg); margin-bottom: var(--space-xl); display: flex; align-items: flex-start; gap: var(--space-md);">
            <span class="material-symbols-outlined text-primary" style="font-size: 24px; flex-shrink: 0;">info</span>
            <div>
                <span class="font-title-md text-primary" style="display: block; margin-bottom: var(--space-xs);">Catatan Kewenangan</span>
                <p class="font-body-md text-on-surface-variant" style="margin: 0;">
                    Setelah pengecekan selesai, hasil akan dikirimkan ke <strong>Fasilitator</strong> untuk validasi akhir dan disposisi.
                    Kerusakan yang memerlukan perbaikan akan diteruskan ke tim teknisi melalui Fasilitator.
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-md); padding: var(--space-lg) 0;">
            <button type="button" onclick="document.querySelector('form').action='{{ route('go.pengecekan-kamar.draft', $checkout->id_pengajuan) }}'; document.querySelector('form').submit();" class="font-label-md text-on-surface-variant" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 10px 20px; border-radius: 999px; border: 1px solid var(--outline-variant); background: transparent; cursor: pointer;">
                <span class="material-symbols-outlined" style="font-size: 20px;">save</span>
                Batal & Simpan Draf
            </button>

            <div style="display: flex; gap: var(--space-md); flex-wrap: wrap;">
                <a href="{{ route('go.laporan-kerusakan.index') }}" class="font-label-md text-primary" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 10px 20px; border-radius: 999px; border: 1px solid var(--outline-variant); text-decoration: none;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">report</span>
                    Catat Kerusakan Terpisah
                </a>

                <button type="submit" class="bg-secondary text-on-secondary font-label-md" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 10px 24px; border-radius: 999px; border: none; cursor: pointer;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">send</span>
                    Selesaikan Pengecekan & Kirim ke Fasilitator
                </button>
            </div>
        </div>

    </form>

</div>

{{-- Summary JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[type="radio"][value="baik"], input[type="radio"][value="rusak_ringan"], input[type="radio"][value="rusak_berat"]');

        function updateCounts() {
            const baik = document.querySelectorAll('input[type="radio"][value="baik"]:checked').length;
            const rusakRingan = document.querySelectorAll('input[type="radio"][value="rusak_ringan"]:checked').length;
            const rusakBerat = document.querySelectorAll('input[type="radio"][value="rusak_berat"]:checked').length;

            document.getElementById('count-baik').textContent = baik;
            document.getElementById('count-rusak-ringan').textContent = rusakRingan;
            document.getElementById('count-rusak-berat').textContent = rusakBerat;
            document.getElementById('count-total').textContent = baik + rusakRingan + rusakBerat;
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', updateCounts);
        });

        // Timer
        let seconds = 0;
        const timerEl = document.getElementById('timer');
        setInterval(function () {
            seconds++;
            const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            timerEl.textContent = h + ':' + m + ':' + s;
        }, 1000);
    });
</script>
@endsection

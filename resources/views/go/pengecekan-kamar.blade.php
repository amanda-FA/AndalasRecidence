@extends('layouts.app')

@section('title', 'Pengecekan Kamar')

@section('navbar-left')
    <span class="material-symbols-outlined">door_open</span>
    <span class="font-title-md">Pengecekan Kamar</span>
@endsection

@section('content')
<div class="bg-surface-container-lowest min-h-screen" style="padding: var(--space-lg);">

    {{-- Header --}}
    <div style="margin-bottom: var(--space-xl);">
        <h1 class="font-headline-lg text-primary" style="margin-bottom: var(--space-xs);">Pengecekan Kamar</h1>
        <p class="font-body-md text-on-surface-variant">Kelola antrean inspeksi kamar mahasiswa yang telah mengajukan check-out.</p>
    </div>

    {{-- SOP Banner --}}
    <div class="bg-primary-container" style="border-radius: 16px; padding: var(--space-lg); margin-bottom: var(--space-xl); display: flex; align-items: flex-start; gap: var(--space-md);">
        <span class="material-symbols-outlined text-primary" style="font-size: 28px; flex-shrink: 0;">gavel</span>
        <div>
            <span class="font-title-md text-primary" style="display: block; margin-bottom: var(--space-xs);">Standar Operasional Prosedur (SOP)</span>
            <p class="font-body-md text-on-surface-variant" style="margin: 0;">
                General Officer berwenang melakukan inspeksi fisik kamar, verifikasi kunci & RFID, dan mencatat kerusakan.
                Jika ditemukan kerusakan di luar kewenangan GO, hasil pengecekan akan diteruskan ke <strong>Fasilitator</strong> untuk disposisi lebih lanjut.
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('go.pengecekan-kamar.index') }}" style="display: flex; flex-wrap: wrap; gap: var(--space-md); margin-bottom: var(--space-xl); align-items: flex-end;">
        <div style="flex: 1; min-width: 180px;">
            <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Gedung</label>
            <select name="gedung" onchange="this.form.submit()" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
                <option value="all">Semua Gedung</option>
                @foreach ($gedungList as $id => $nama)
                    <option value="{{ $id }}" {{ $gedungFilter == $id ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Status</label>
            <select name="status" onchange="this.form.submit()" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ $statusFilter == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="sedang_diperiksa" {{ $statusFilter == 'sedang_diperiksa' ? 'selected' : '' }}>Sedang Diperiksa</option>
                <option value="selesai" {{ $statusFilter == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label class="font-label-md text-on-surface-variant" style="display: block; margin-bottom: var(--space-xs);">Urutkan</label>
            <select name="sort" onchange="this.form.submit()" class="font-body-md text-primary" style="width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
                <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="terlama" {{ $sort == 'terlama' ? 'selected' : '' }}>Terlama</option>
            </select>
        </div>
    </form>

    {{-- 3-Step Guide --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-xl);">

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant); text-align: center;">
            <div class="bg-primary-container" style="width: 48px; height: 48px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: var(--space-sm);">
                <span class="font-title-md text-primary">1</span>
            </div>
            <span class="font-title-md text-primary" style="display: block; margin-bottom: var(--space-xs);">Inspeksi Fisik</span>
            <p class="font-body-md text-on-surface-variant" style="margin: 0;">Periksa kondisi fisik kamar, perabot, dan fasilitas sesuai checklist.</p>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant); text-align: center;">
            <div class="bg-primary-container" style="width: 48px; height: 48px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: var(--space-sm);">
                <span class="font-title-md text-primary">2</span>
            </div>
            <span class="font-title-md text-primary" style="display: block; margin-bottom: var(--space-xs);">Serah Terima RFID</span>
            <p class="font-body-md text-on-surface-variant" style="margin: 0;">Verifikasi kartu RFID dan kunci fisik dari mahasiswa.</p>
        </div>

        <div class="bg-surface-container-lowest" style="border-radius: 16px; padding: var(--space-lg); border: 1px solid var(--outline-variant); text-align: center;">
            <div class="bg-primary-container" style="width: 48px; height: 48px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: var(--space-sm);">
                <span class="font-title-md text-primary">3</span>
            </div>
            <span class="font-title-md text-primary" style="display: block; margin-bottom: var(--space-xs);">Penerusan ke Fasilitator</span>
            <p class="font-body-md text-on-surface-variant" style="margin: 0;">Kirim hasil pengecekan ke Fasilitator untuk validasi akhir.</p>
        </div>
    </div>

    {{-- Antrean Table --}}
    <div class="bg-surface-container-lowest" style="border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden;">
        <div style="padding: var(--space-lg); border-bottom: 1px solid var(--outline-variant);">
            <h2 class="font-headline-sm text-primary">Antrean Pengecekan</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--outline-variant);">
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Mahasiswa</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Gedung</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Kamar</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Tanggal Check-out</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: left;">Status</th>
                        <th class="font-label-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md); text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($antrean as $item)
                        @php
                            $namaMhs = is_array($item) ? ($item['nama'] ?? '-') : ($item->pengguna->nama_lengkap ?? '-');
                            $namaGedung = is_array($item) ? ($item['gedung'] ?? '-') : ($item->gedung->nama_gedung ?? '-');
                            $nomorKmr = is_array($item) ? ($item['kamar'] ?? '-') : ($item->kamar->nomor_kamar ?? '-');
                            $tglCheckout = is_array($item) ? ($item['tanggal_checkout_formatted'] ?? '-') : (\Carbon\Carbon::parse($item->tanggal_checkout ?? now())->format('d M Y'));
                            $statusVal = is_array($item) ? ($item['status'] ?? 'menunggu') : ($item->status ?? 'menunggu');
                            $idPengajuan = is_array($item) ? ($item['id_pengajuan'] ?? 1) : ($item->id_pengajuan ?? 1);
                        @endphp
                        <tr style="border-bottom: 1px solid var(--outline-variant);">
                            <td style="padding: var(--space-sm) var(--space-md);">
                                <div style="display: flex; align-items: center; gap: var(--space-sm);">
                                    <div class="bg-primary-container" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span class="font-label-md text-primary">{{ strtoupper(substr($namaMhs, 0, 1)) }}</span>
                                    </div>
                                    <span class="font-body-md text-primary">{{ $namaMhs }}</span>
                                </div>
                            </td>
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">{{ $namaGedung }}</td>
                            <td class="font-body-md text-primary" style="padding: var(--space-sm) var(--space-md);">{{ $nomorKmr }}</td>
                            <td class="font-body-md text-on-surface-variant" style="padding: var(--space-sm) var(--space-md);">
                                {{ $tglCheckout }}
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md);">
                                @php
                                    $statusColors = [
                                        'menunggu' => 'background: var(--warning-container); color: var(--on-warning-container);',
                                        'sedang_diperiksa' => 'background: var(--info-container); color: var(--on-info-container);',
                                        'dalam_pengecekan' => 'background: var(--info-container); color: var(--on-info-container);',
                                        'selesai' => 'background: var(--success-container); color: var(--on-success-container);',
                                    ];
                                    $statusLabels = [
                                        'menunggu' => 'Menunggu Pengecekan',
                                        'sedang_diperiksa' => 'Dalam Pengecekan',
                                        'dalam_pengecekan' => 'Dalam Pengecekan',
                                        'selesai' => 'Selesai Pengecekan',
                                    ];
                                @endphp
                                <span class="font-label-md" style="{{ $statusColors[$statusVal] ?? 'background: var(--surface-container); color: var(--on-surface);' }} padding: 4px 12px; border-radius: 999px;">
                                    {{ $statusLabels[$statusVal] ?? ucfirst($statusVal) }}
                                </span>
                            </td>
                            <td style="padding: var(--space-sm) var(--space-md); text-align: center;">
                                @if ($statusVal === 'menunggu')
                                    <form action="{{ route('go.pengecekan-kamar.mulai', $idPengajuan) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="bg-secondary text-on-secondary font-label-md" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 8px 16px; border-radius: 999px; border: none; cursor: pointer;">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">play_arrow</span>
                                            Mulai
                                        </button>
                                    </form>
                                @elseif ($statusVal === 'sedang_diperiksa' || $statusVal === 'dalam_pengecekan')
                                    <a href="{{ route('go.pengecekan-kamar.show', $idPengajuan) }}" class="bg-primary-container text-primary font-label-md" style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 8px 16px; border-radius: 999px; text-decoration: none;">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">edit_note</span>
                                        Lanjutkan
                                    </a>
                                @else
                                    <span class="font-caption-sm text-on-surface-variant">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: var(--space-2xl); text-align: center;">
                                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 48px; display: block; margin-bottom: var(--space-sm);">inbox</span>
                                <span class="font-body-md text-on-surface-variant">Belum ada antrean pengecekan kamar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

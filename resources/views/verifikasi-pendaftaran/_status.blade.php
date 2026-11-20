@php
    $label = \App\Http\Controllers\Pendaftaran\VerifikasiPendaftaranController::STATUSES[$status] ?? ucfirst(str_replace('_', ' ', $status));
    $color = match ($status) { 'terverifikasi' => 'bg-success-container text-success', 'ditolak', 'batal' => 'bg-error-container text-error', 'perlu_perbaikan' => 'bg-amber-50 text-amber-800', default => 'bg-surface-container text-secondary' };
@endphp
<span class="inline-flex rounded-full px-3 py-1 text-caption-sm font-semibold {{ $color }}">{{ $label }}</span>

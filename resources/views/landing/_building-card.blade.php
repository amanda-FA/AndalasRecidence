<article class="residence-card">
    <a class="residence-card-visual residence-visual-{{ $loop->index % 3 }} {{ $building['foto_url'] ? 'has-photo' : '' }}" href="{{ route('hunian.show', $building['id']) }}" aria-label="Lihat detail {{ $building['nama'] }}">
        <span class="residence-building-label">{{ $building['label'] }}</span>
        @if ($building['foto_url'])
            <img class="residence-building-photo" src="{{ $building['foto_url'] }}" alt="Foto {{ $building['nama'] }}" loading="lazy">
        @else
        <svg class="residence-building-drawing" viewBox="0 0 400 210" fill="none" aria-hidden="true"><path d="M40 180H360M95 180V63L210 25V180M210 46L305 75V180M83 67L210 14L317 69" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/><path d="M119 82L144 74V100L119 107ZM168 66L192 58V84L168 92ZM119 124L144 116V142L119 150ZM168 109L192 101V127L168 135ZM231 76L253 83V107L231 100ZM271 89L291 95V119L271 113ZM231 118L253 125V149L231 142ZM271 131L291 137V161L271 155Z" fill="currentColor" opacity=".3"/><path d="M168 180V151L192 143V180M55 180V149M342 180V139M48 148C32 128 57 116 64 130C76 144 64 153 55 149M331 140C310 116 340 102 351 119C367 136 349 148 342 140" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/></svg>
        @endif
        <span class="residence-visual-caption">Hunian kampus</span><span class="residence-card-arrow material-symbols-outlined" aria-hidden="true">north_east</span>
    </a>
    <div class="residence-card-body"><div class="residence-card-status {{ $building['kamar_tersedia'] ? 'is-available' : '' }}"><span></span>{{ $building['kamar_tersedia'] ? $building['kamar_tersedia'].' kamar tersedia' : ($building['jumlah_kamar'] ? 'Belum ada kamar tersedia' : 'Informasi kamar belum tersedia') }}</div><h3><a href="{{ route('hunian.show', $building['id']) }}">{{ $building['nama'] }}</a></h3><p class="residence-card-location"><span class="material-symbols-outlined" aria-hidden="true">location_on</span>Lingkungan Universitas Andalas</p>
        <div class="residence-card-facilities">@forelse ($building['fasilitas']->take(3) as $facility)<span>{{ $facility }}</span>@empty<span>Informasi fasilitas menyusul</span>@endforelse</div>
        <p class="residence-card-type">{{ $building['tipe']->take(2)->implode(' · ') ?: 'Tipe kamar belum tercatat' }}</p>
        <div class="residence-card-bottom"><div><small>Tarif mulai</small><strong>{{ $building['tarif_mulai'] !== null ? 'Rp '.number_format((float) $building['tarif_mulai'], 0, ',', '.') : 'Belum tercatat' }}</strong></div><a href="{{ route('hunian.show', $building['id']) }}">Lihat Detail <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a></div>
    </div>
</article>

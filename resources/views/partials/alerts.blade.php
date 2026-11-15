@if (session('success'))
    <div class="mb-space-lg p-space-md rounded-xl bg-[#2E9E5B]/10 text-[#1F7A43] flex items-center gap-space-sm" role="alert">
        <span class="material-symbols-outlined text-[20px]">check_circle</span>
        <span class="font-body-md-medium text-body-md-medium">{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-space-lg p-space-md rounded-xl bg-error-container text-on-error-container flex items-center gap-space-sm" role="alert">
        <span class="material-symbols-outlined text-[20px]">error</span>
        <span class="font-body-md-medium text-body-md-medium">{{ session('error') }}</span>
    </div>
@endif

@if (session('warning'))
    <div class="mb-space-lg p-space-md rounded-xl bg-[#C97D1A]/15 text-[#9C5E0E] flex items-center gap-space-sm" role="alert">
        <span class="material-symbols-outlined text-[20px]">warning</span>
        <span class="font-body-md-medium text-body-md-medium">{{ session('warning') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="mb-space-lg p-space-md rounded-xl bg-error-container text-on-error-container flex flex-col gap-1" role="alert">
        <div class="flex items-center gap-space-sm font-label-md text-label-md">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <span>Terjadi kesalahan pada input:</span>
        </div>
        <ul class="list-disc list-inside font-caption-sm text-caption-sm pl-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

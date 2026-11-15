<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use RuntimeException;

class FileStorageService
{
    /**
     * Daftar kategori dan format file yang diperbolehkan.
     */
    private array $allowedTypes = [
        'pendaftaran' => ['pdf', 'jpg', 'jpeg', 'png'],
        'pembayaran' => ['pdf', 'jpg', 'jpeg', 'png'],
        'kerusakan' => ['jpg', 'jpeg', 'png'],
        'surat-checkout' => ['pdf'],
        'surat-bebas-asrama' => ['pdf'],
    ];

    /**
     * Batas maksimal ukuran file: 10 MB.
     */
    private int $maxFileSize = 10240;

    /**
     * Memvalidasi file berdasarkan kategori.
     */
    public function validate(UploadedFile $file, string $category): void
    {
        if (!array_key_exists($category, $this->allowedTypes)) {
            throw new InvalidArgumentException(
                'Kategori file tidak dikenali.'
            );
        }

        $extensions = implode(',', $this->allowedTypes[$category]);

        Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'file',
                    'max:' . $this->maxFileSize,
                    'mimes:' . $extensions,
                    'extensions:' . $extensions,
                ],
            ],
            [
                'file.max' => 'Ukuran file maksimal adalah 10 MB.',
                'file.mimes' => 'Format file tidak sesuai dengan kategori.',
                'file.extensions' => 'Ekstensi file tidak sesuai dengan kategori.',
            ]
        )->validate();
    }

    /**
     * Menyimpan file setelah validasi berhasil.
     */
    public function store(
        UploadedFile $file,
        string $category,
        string $disk = 'local'
    ): string {
        $this->validate($file, $category);

        $path = $file->store($category, $disk);

        if ($path === false) {
            throw new RuntimeException('Gagal menyimpan file.');
        }

        return $path;
    }

    /**
     * Menghapus file berdasarkan path.
     */
    public function delete(
        ?string $path,
        string $disk = 'local'
    ): bool {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }

    /**
     * Memeriksa keberadaan file.
     */
    public function exists(
        ?string $path,
        string $disk = 'local'
    ): bool {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->exists($path);
    }
}

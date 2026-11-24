<?php

namespace App\Services;

use App\Models\Client;
use App\Models\JenisClient;
use App\Models\Pembayaran;
use App\Models\PendaftaranHunian;
use App\Models\TarifHunian;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranService
{
    public const KATEGORI_MANDIRI = [
        'reguler',
        's1_non_maba',
        'international_private',
        'guest',
    ];

    /**
     * Memeriksa kategori pembayaran mandiri.
     */
    public function dapatMembayarMandiri(
        ?string $kategori
    ): bool {
        return in_array(
            $kategori,
            self::KATEGORI_MANDIRI,
            true
        );
    }

    /**
     * Mengambil pendaftaran terakhir Client.
     */
    public function getPendaftaran(
        Client $client
    ): ?PendaftaranHunian {
        return PendaftaranHunian::with([
            'client.jenisClient',
            'kamar.gedung',
            'tipeKamar',
        ])
            ->where('id_client', $client->id_client)
            ->orderByDesc('id_pendaftaran')
            ->first();
    }

    /**
     * Mengidentifikasi kategori Client.
     */
    public function getKategori(Client $client): ?string
    {
        $kategori = array_search(
            $client->jenisClient?->jenis,
            JenisClient::CATEGORIES,
            true
        );

        return $kategori === false ? null : $kategori;
    }

    /**
     * Menentukan status pendaftaran.
     */
    public function getStatus(
        ?PendaftaranHunian $pendaftaran
    ): string {
        if (! $pendaftaran) {
            return 'belum_mendaftar';
        }

        if ($pendaftaran->status_pendaftaran === 'terverifikasi') {
            return 'siap_penerbitan_tagihan';
        }

        return $pendaftaran->status_pendaftaran;
    }

    /**
     * Informasi skema pembayaran berdasarkan kategori.
     */
    public function getSkemaPembayaran(
        ?string $kategori
    ): array {
        return match ($kategori) {

            'reguler' => [
                'jenis' => 'mandiri',
                'satuan' => 'Tahunan per orang',
                'keterangan' =>
                    'Pembayaran dilakukan sebelum check-in.',
            ],

            's1_non_maba' => [
                'jenis' => 'mandiri',
                'satuan' => 'Tahunan per orang',
                'keterangan' =>
                    'Pembayaran dilakukan sebelum check-in. '
                    . 'Pembayaran bulanan memerlukan '
                    . 'verifikasi pengelola dan tarif resmi.',
            ],

            'international_private' => [
                'jenis' => 'mandiri',
                'satuan' => 'Sesuai ketentuan masa hunian',
                'keterangan' =>
                    'Pembayaran dilakukan secara mandiri. '
                    . 'Skema tarif dan masa hunian '
                    . 'harus ditetapkan pengelola.',
            ],

            'guest' => [
                'jenis' => 'mandiri',
                'satuan' => 'Harian',
                'keterangan' =>
                    'Pembayaran berdasarkan tarif kamar '
                    . 'dan durasi penyewaan.',
            ],

            'kipk' => [
                'jenis' => 'pembiayaan_khusus',
                'satuan' => 'Pemotongan dana KIP-K',
                'keterangan' =>
                    'Invoice bernilai nol sementara. '
                    . 'Pembiayaan dilakukan melalui KIP-K.',
            ],

            'international_funded' => [
                'jenis' => 'pihak_ketiga',
                'satuan' => 'Pembiayaan beasiswa',
                'keterangan' =>
                    'Tagihan ditangani oleh pihak '
                    . 'penanggung biaya sesuai ketentuan beasiswa.',
            ],

            'summer_course' => [
                'jenis' => 'pihak_ketiga',
                'satuan' => 'Pembiayaan penyelenggara',
                'keterangan' =>
                    'Tagihan ditujukan kepada '
                    . 'penyelenggara Summer Course.',
            ],

            default => [
                'jenis' => 'tidak_diketahui',
                'satuan' => '-',
                'keterangan' =>
                    'Kategori Client belum dikenali.',
            ],
        };
    }

    /**
     * Mengambil tarif berdasarkan gedung dan tipe kamar.
     */
    private function getTarif(
        PendaftaranHunian $pendaftaran,
        string $kategoriTarif,
        string $satuanTarif
    ): TarifHunian {
        $kamar = $pendaftaran->kamar;

        if (! $kamar) {
            throw new \DomainException(
                'Data kamar belum tersedia.'
            );
        }

        $tarif = TarifHunian::where(
            'id_gedung',
            $kamar->id_gedung
        )
            ->where(
                'id_tipeKamar',
                $kamar->id_tipeKamar
            )
            ->where('kategori_tarif', $kategoriTarif)
            ->where('satuan_tarif', $satuanTarif)
            ->first();

        if (! $tarif) {
            throw new \DomainException(
                'Tarif untuk gedung dan tipe kamar '
                . 'ini belum tersedia.'
            );
        }

        if ($tarif->nominal <= 0) {
            throw new \DomainException(
                'Nominal tarif tidak valid.'
            );
        }

        return $tarif;
    }

    /**
     * Menghitung durasi penyewaan harian.
     */
    private function hitungDurasiHarian(
        PendaftaranHunian $pendaftaran
    ): int {
        if (
            ! $pendaftaran->tanggal_masuk ||
            ! $pendaftaran->rencana_keluar
        ) {
            throw new \DomainException(
                'Tanggal masuk dan rencana keluar '
                . 'belum tersedia.'
            );
        }

        $tanggalMasuk = Carbon::parse(
            $pendaftaran->tanggal_masuk
        )->startOfDay();

        $tanggalKeluar = Carbon::parse(
            $pendaftaran->rencana_keluar
        )->startOfDay();

        if ($tanggalKeluar->lte($tanggalMasuk)) {
            throw new \DomainException(
                'Tanggal keluar harus setelah tanggal masuk.'
            );
        }

        return (int) $tanggalMasuk->diffInDays(
            $tanggalKeluar
        );
    }

    /**
     * Menghitung nominal tagihan Client.
     */
    public function hitungTarif(
        PendaftaranHunian $pendaftaran
    ): string {
        if ($pendaftaran->status_pendaftaran !== 'terverifikasi') {
            throw new \DomainException(
                'Pendaftaran belum terverifikasi.'
            );
        }

        $pendaftaran->loadMissing([
            'client.jenisClient',
            'kamar',
        ]);

        $kategori = $this->getKategori(
            $pendaftaran->client
        );

        switch ($kategori) {

            // Pembayaran tahunan mahasiswa lokal.
            case 'reguler':
            case 's1_non_maba':

                $tarif = $this->getTarif(
                    $pendaftaran,
                    'mahasiswa',
                    'tahun_orang'
                );

                return (string) $tarif->nominal;

            // Pembayaran mandiri internasional.
            // Memerlukan penetapan skema tarif oleh pengelola.
            case 'international_private':

                throw new \DomainException(
                    'Skema tarif dan masa hunian '
                    . 'belum ditetapkan oleh pengelola.'
                );

            // Penghuni umum menggunakan tarif harian.
            case 'guest':

                $tarif = $this->getTarif(
                    $pendaftaran,
                    'umum',
                    'hari'
                );

                $durasi = $this->hitungDurasiHarian(
                    $pendaftaran
                );

                return number_format(
                    $tarif->nominal * $durasi,
                    2,
                    '.',
                    ''
                );

            // Invoice nol sementara untuk KIP-K.
            case 'kipk':

                return '0.00';

            // Pembiayaan melalui pihak ketiga.
            case 'international_funded':
            case 'summer_course':

                $skema = $this->getSkemaPembayaran(
                    $kategori
                );

                throw new \DomainException(
                    $skema['keterangan']
                );

            default:

                throw new \DomainException(
                    'Kategori Client belum dikenali.'
                );
        }
    }

    /**
     * Mengambil tagihan yang sudah diterbitkan.
     */
    public function getPembayaran(
        PendaftaranHunian $pendaftaran
    ): ?Pembayaran {
        return Pembayaran::where(
            'id_pendaftaran',
            $pendaftaran->id_pendaftaran
        )
            ->latest('id_pembayaran')
            ->first();
    }

    /**
     * Menerbitkan tagihan berdasarkan kategori Client.
     */
    public function terbitkanTagihan(
        PendaftaranHunian $pendaftaran
    ): Pembayaran {
        return DB::transaction(function () use ($pendaftaran) {

            $record = PendaftaranHunian::with([
                'client.jenisClient',
                'tipeKamar',
                'kamar',
            ])
                ->where(
                    'id_pendaftaran',
                    $pendaftaran->id_pendaftaran
                )
                ->lockForUpdate()
                ->firstOrFail();

            if ($record->status_pendaftaran !== 'terverifikasi') {
                throw new \DomainException(
                    'Pendaftaran belum terverifikasi.'
                );
            }

            $kategori = $this->getKategori(
                $record->client
            );

            // Pembiayaan sponsor dan penyelenggara
            // tidak diterbitkan sebagai tagihan mandiri.
            if (
                ! $this->dapatMembayarMandiri($kategori)
                && $kategori !== 'kipk'
            ) {
                $skema = $this->getSkemaPembayaran(
                    $kategori
                );

                throw new \DomainException(
                    $skema['keterangan']
                );
            }

            // Mencegah penerbitan tagihan ganda.
            $pembayaran = $this->getPembayaran($record);

            if ($pembayaran) {
                return $pembayaran;
            }

            if (! $record->kamar) {
                throw new \DomainException(
                    'Data kamar belum tersedia.'
                );
            }

            $jumlah = $this->hitungTarif($record);

            if ($jumlah < 0 || $jumlah > 9999999999.99) {
                throw new \DomainException(
                    'Nominal tagihan tidak valid.'
                );
            }

            /*
             * Invoice sementara KIP-K.
             * Nominal nol tidak berarti sudah lunas.
             */
            if ($kategori === 'kipk') {

                return Pembayaran::create([
                    'id_pendaftaran' => $record->id_pendaftaran,
                    'jumlah' => '0.00',
                    'metode_bayar' => 'kipk',
                    'bukti_bayar' => null,
                    'tanggal_bayar' => null,
                    'status_pembayaran' => 'menunggu_kipk',
                ]);
            }

            /*
             * Pembayaran mandiri.
             */
            if ($jumlah <= 0) {
                throw new \DomainException(
                    'Nominal tagihan mandiri harus lebih dari nol.'
                );
            }

            return Pembayaran::create([
                'id_pendaftaran' => $record->id_pendaftaran,
                'jumlah' => $jumlah,
                'metode_bayar' => 'belum_dipilih',
                'bukti_bayar' => null,
                'tanggal_bayar' => null,
                'status_pembayaran' => 'menunggu',
            ]);
        });
    }
}

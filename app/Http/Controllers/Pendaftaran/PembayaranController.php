<?php

namespace App\Http\Controllers\Pendaftaran;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Pembayaran;
use App\Models\PendaftaranHunian;
use App\Services\PembayaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    /**
     * Mengambil data Client yang sedang login.
     */
    private function client(Request $request): Client
    {
        return Client::with('jenisClient')
            ->where(
                'id_pengguna',
                $request->user()->getAuthIdentifier()
            )
            ->firstOrFail();
    }

    /**
     * Menampilkan halaman pembayaran Client.
     */
    public function index(
        Request $request,
        PembayaranService $service
    ) {
        $client = $this->client($request);

        $pendaftaran = $service->getPendaftaran($client);

        $pembayaran = $pendaftaran
            ? $service->getPembayaran($pendaftaran)
            : null;

        $kategori = $service->getKategori($client);

        $status = $service->getStatus($pendaftaran);

        $skema = $service->getSkemaPembayaran($kategori);

        $tarif = null;
        $pesan = null;

        $dapatTerbitkanTagihan = false;
        $dapatSimulasi = false;

        $isLocal = app()->environment('local');

        /*
        |--------------------------------------------------------------------------
        | Menentukan skema pembayaran Client
        |--------------------------------------------------------------------------
        */

        if ($status === 'siap_penerbitan_tagihan') {

            switch ($kategori) {

                /*
                 * Mahasiswa penerima KIP-K.
                 */
                case 'kipk':

                    $status = 'pembiayaan_kipk';

                    $pesan = $skema['keterangan'];

                    if ($pembayaran) {

                        $tarif = (string) $pembayaran->jumlah;

                    } elseif ($pendaftaran->kamar) {

                        $tarif = '0.00';

                        $dapatTerbitkanTagihan = $isLocal;

                    } else {

                        $pesan .= ' Menunggu penempatan kamar.';
                    }

                    break;

                /*
                 * Mahasiswa dengan pembiayaan sponsor.
                 */
                case 'international_funded':

                    $status = 'pembiayaan_pihak_ketiga';

                    $pesan = $skema['keterangan'];

                    break;

                /*
                 * Peserta Summer Course.
                 */
                case 'summer_course':

                    $status = 'pembiayaan_penyelenggara';

                    $pesan = $skema['keterangan'];

                    break;

                /*
                 * Seluruh kategori pembayaran mandiri.
                 */
                case 'reguler':
                case 's1_non_maba':
                case 'international_private':
                case 'guest':

                    $pesan = $skema['keterangan'];

                    /*
                     * Gunakan nominal tagihan yang sudah
                     * tersimpan agar tidak berubah ketika
                     * master tarif diperbarui.
                     */

                    if ($pembayaran) {

                        $tarif = (string) $pembayaran->jumlah;

                    } else {

                        try {

                            $tarif = $service->hitungTarif(
                                $pendaftaran
                            );

                            if ($tarif <= 0 || $tarif > 9999999999.99) {

                                throw new \DomainException(
                                    'Nominal tagihan tidak valid.'
                                );
                            }

                            $dapatTerbitkanTagihan = $isLocal;

                        } catch (\DomainException $e) {

                            $status = 'tarif_belum_tersedia';

                            $pesan = $e->getMessage();
                        }
                    }

                    break;

                /*
                 * Kategori belum dikenali.
                 */
                default:

                    $status = 'kategori_belum_dikenali';

                    $pesan = 'Kategori Client belum dikenali.';

                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Menentukan kelayakan simulasi pembayaran
        |--------------------------------------------------------------------------
        */

        if (
            $isLocal
            && $pendaftaran
            && $pendaftaran->status_pendaftaran === 'terverifikasi'
            && $service->dapatMembayarMandiri($kategori)
            && $pembayaran
        ) {
            $dapatSimulasi = $this->tagihanDapatDisimulasikan(
                $pembayaran
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Menampilkan halaman pembayaran
        |--------------------------------------------------------------------------
        */

        return view('pembayaran.index', compact(
            'client',
            'pendaftaran',
            'pembayaran',
            'kategori',
            'status',
            'tarif',
            'pesan',
            'skema',
            'dapatTerbitkanTagihan',
            'dapatSimulasi',
            'isLocal'
        ));
    }

    /**
     * Menerbitkan tagihan pembayaran.
     */
    public function terbitkan(
        Request $request,
        PembayaranService $service
    ) {
        /*
         * Penerbitan tagihan pengujian hanya
         * tersedia di lingkungan lokal.
         */

        abort_unless(
            app()->environment('local'),
            403
        );

        $data = $request->validate([
            'id_pendaftaran' => 'required|integer',
        ]);

        $client = $this->client($request);

        /*
         * Memastikan pendaftaran merupakan
         * milik Client yang sedang login.
         */

        $pendaftaran = PendaftaranHunian::where(
            'id_client',
            $client->id_client
        )
            ->where(
                'id_pendaftaran',
                $data['id_pendaftaran']
            )
            ->firstOrFail();

        $kategori = $service->getKategori($client);

        /*
         * Penerbitan tagihan hanya untuk kategori
         * pembayaran mandiri dan KIP-K.
         */

        if (
            ! $service->dapatMembayarMandiri($kategori)
            && $kategori !== 'kipk'
        ) {

            return back()->withErrors([
                'pembayaran' =>
                    'Kategori ini tidak menggunakan '
                    . 'penerbitan tagihan mandiri.',
            ]);
        }

        try {

            $pembayaran = $service->terbitkanTagihan(
                $pendaftaran
            );

        } catch (\DomainException $e) {

            return back()->withErrors([
                'pembayaran' => $e->getMessage(),
            ]);
        }

        /*
         * Pesan khusus invoice sementara KIP-K.
         */

        if ($pembayaran->status_pembayaran === 'menunggu_kipk') {

            return redirect()
                ->route('pembayaran.index')
                ->with(
                    'success',
                    'Invoice sementara KIP-K tersedia. '
                    . 'Pembiayaan masih menunggu proses KIP-K.'
                );
        }

        return redirect()
            ->route('pembayaran.index')
            ->with(
                'success',
                'Tagihan pengujian tersedia.'
            );
    }

    /**
     * Memeriksa kelayakan simulasi pembayaran.
     */
    private function tagihanDapatDisimulasikan(
        Pembayaran $bayar
    ): bool {
        return in_array(
            $bayar->status_pembayaran,
            [
                'menunggu',
                'simulasi_gagal',
            ],
            true
        )
            && in_array(
                $bayar->metode_bayar,
                [
                    'belum_dipilih',
                    'simulasi_va',
                    'simulasi_qris',
                    'simulasi_loket',
                ],
                true
            )
            && $bayar->tanggal_bayar === null
            && $bayar->bukti_bayar === null
            && $bayar->jumlah > 0;
    }

    /**
     * Melakukan simulasi pembayaran.
     */
    public function simulasi(
        Request $request,
        PembayaranService $service
    ) {
        /*
         * Simulasi tidak boleh dijalankan
         * di lingkungan production.
         */

        abort_unless(
            app()->environment('local'),
            403
        );

        $data = $request->validate([
            'id_pendaftaran' => 'required|integer',
            'metode' => 'required|in:va,qris,loket',
            'hasil' => 'required|in:berhasil,gagal',
        ]);

        $client = $this->client($request);

        try {

            DB::transaction(function () use (
                $data,
                $client,
                $service
            ) {

                /*
                 * Mengunci data pendaftaran agar
                 * tidak diproses secara bersamaan.
                 */

                $record = PendaftaranHunian::with(
                    'client.jenisClient'
                )
                    ->where(
                        'id_client',
                        $client->id_client
                    )
                    ->where(
                        'id_pendaftaran',
                        $data['id_pendaftaran']
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $kategori = $service->getKategori(
                    $record->client
                );

                /*
                 * Validasi status pendaftaran dan
                 * kategori pembayaran mandiri.
                 */

                if (
                    $record->status_pendaftaran !== 'terverifikasi'
                    || ! $service->dapatMembayarMandiri($kategori)
                ) {

                    throw new \DomainException(
                        'Pendaftaran belum memenuhi '
                        . 'syarat simulasi pembayaran.'
                    );
                }

                /*
                 * Mengunci tagihan pembayaran.
                 */

                $bayar = Pembayaran::where(
                    'id_pendaftaran',
                    $record->id_pendaftaran
                )
                    ->latest('id_pembayaran')
                    ->lockForUpdate()
                    ->first();

                if (! $bayar) {

                    throw new \DomainException(
                        'Tagihan pembayaran belum tersedia.'
                    );
                }

                /*
                 * Mencegah simulasi terhadap tagihan
                 * lunas, pembiayaan khusus, atau
                 * pembayaran yang tidak valid.
                 */

                if (! $this->tagihanDapatDisimulasikan($bayar)) {

                    throw new \DomainException(
                        'Tagihan tidak dapat diproses '
                        . 'melalui simulasi.'
                    );
                }

                /*
                 * Menyimpan hasil simulasi.
                 *
                 * Tidak mengisi tanggal bayar maupun
                 * bukti bayar karena tidak ada
                 * transaksi keuangan sebenarnya.
                 */

                $bayar->update([
                    'metode_bayar' =>
                        'simulasi_' . $data['metode'],

                    'status_pembayaran' =>
                        $data['hasil'] === 'berhasil'
                            ? 'simulasi_lunas'
                            : 'simulasi_gagal',
                ]);
            });

        } catch (\DomainException $e) {

            return back()->withErrors([
                'pembayaran' => $e->getMessage(),
            ]);
        }

        /*
         * Menampilkan pesan sesuai hasil simulasi.
         */

        if ($data['hasil'] === 'gagal') {

            return redirect()
                ->route('pembayaran.index')
                ->with(
                    'warning',
                    'Simulasi pembayaran gagal. '
                    . 'Silakan mencoba kembali.'
                );
        }

        return redirect()
            ->route('pembayaran.index')
            ->with(
                'success',
                'Simulasi pembayaran berhasil. '
                . 'Tidak ada uang yang ditransfer.'
            );
    }
}

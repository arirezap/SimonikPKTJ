<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveiJawabanModel extends Model
{
    protected $table            = 'survei_pemetaan_jawaban';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'respon_id',
        'user_id',
        'nomor_pertanyaan',
        'kode_bagian',
        'jawaban_teks',
        'jawaban_angka',
        'jawaban_pilihan',
        'jawaban_lainnya',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil peta seluruh jawaban seorang responden [nomor_pertanyaan => array].
     */
    public function getAnswersMap(int $responId): array
    {
        $rows = $this->where('respon_id', $responId)
                     ->orderBy('nomor_pertanyaan', 'ASC')
                     ->findAll();

        $map = [];
        foreach ($rows as $row) {
            $qNo = (int) $row['nomor_pertanyaan'];
            $pilihan = [];
            if (!empty($row['jawaban_pilihan'])) {
                $decoded = json_decode((string) $row['jawaban_pilihan'], true);
                if (is_array($decoded)) {
                    $pilihan = array_values($decoded);
                }
            }
            $row['pilihan_array'] = $pilihan;
            $map[$qNo] = $row;
        }

        return $map;
    }

    /**
     * Menyimpan / memperbarui sekumpulan jawaban untuk satu sesi respon secara efisien.
     */
    public function upsertAnswers(int $responId, ?int $userId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $existingRows = $this->select('id, nomor_pertanyaan')
                             ->where('respon_id', $responId)
                             ->findAll();
        $existingMap = [];
        foreach ($existingRows as $er) {
            $existingMap[(int) $er['nomor_pertanyaan']] = (int) $er['id'];
        }

        $now = date('Y-m-d H:i:s');
        $db  = \Config\Database::connect();

        foreach ($items as $item) {
            $qNo = (int) ($item['nomor_pertanyaan'] ?? 0);
            if ($qNo < 1 || $qNo > 83) {
                continue;
            }

            $pilihanJson = null;
            if (isset($item['jawaban_pilihan']) && is_array($item['jawaban_pilihan']) && !empty($item['jawaban_pilihan'])) {
                $pilihanJson = json_encode(array_values($item['jawaban_pilihan']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } elseif (isset($item['jawaban_pilihan']) && is_string($item['jawaban_pilihan']) && $item['jawaban_pilihan'] !== '') {
                $pilihanJson = $item['jawaban_pilihan'];
            }

            $rawTeks    = $item['jawaban_teks'] ?? null;
            $rawAngka   = $item['jawaban_angka'] ?? null;
            $rawLainnya = $item['jawaban_lainnya'] ?? null;

            $payload = [
                'respon_id'        => $responId,
                'user_id'          => $userId,
                'nomor_pertanyaan' => $qNo,
                'kode_bagian'      => (string) ($item['kode_bagian'] ?? 'BAGIAN_1'),
                'jawaban_teks'     => ($rawTeks !== null && $rawTeks !== '') ? (string) $rawTeks : null,
                'jawaban_angka'    => ($rawAngka !== null && $rawAngka !== '') ? $rawAngka : null,
                'jawaban_pilihan'  => $pilihanJson,
                'jawaban_lainnya'  => ($rawLainnya !== null && $rawLainnya !== '') ? (string) $rawLainnya : null,
                'updated_at'       => $now,
            ];

            if (isset($existingMap[$qNo])) {
                $db->table($this->table)
                   ->where('id', $existingMap[$qNo])
                   ->where('respon_id', $responId)
                   ->update($payload);
            } else {
                $payload['created_at'] = $now;
                $db->table($this->table)->insert($payload);
            }
        }
    }

    /**
     * Menghapus jawaban yatim (orphan answers) pada cabang kondisional yang tidak berlaku.
     */
    public function deleteOrphanAnswers(int $responId, array $questionNumbers): void
    {
        if (empty($questionNumbers)) {
            return;
        }
        $this->where('respon_id', $responId)
             ->whereIn('nomor_pertanyaan', array_map('intval', $questionNumbers))
             ->delete();
    }
}


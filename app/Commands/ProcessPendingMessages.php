<?php

namespace App\Commands;

use App\Libraries\WhatsappSender;
use App\Models\MessageModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class ProcessPendingMessages extends BaseCommand
{
    protected $group = 'Messages';
    protected $name = 'messages:process-pending';
    protected $description = 'Process pending WhatsApp messages.';

    public function run(array $params)
    {
        $db = Database::connect();
        $messageModel = new MessageModel();

        $messages = $messageModel
            ->where('status', 'pending')
            ->orderBy('waktu_kirim', 'ASC')
            ->findAll(10);

        CLI::write('PENDING_FOUND=' . count($messages));

        $sender = new WhatsappSender();

        foreach ($messages as $message) {
            $id = (int) $message['id'];
            $lockName = 'presensi.message.' . $id;

            $lock = $db->query(
                'SELECT GET_LOCK(?, 0) AS acquired',
                [$lockName]
            )->getRowArray();

            if (!isset($lock['acquired']) || (int) $lock['acquired'] !== 1) {
                CLI::write("MESSAGE_ID={$id} | LOCK=SKIP");
                continue;
            }

            try {
                $current = $messageModel->find($id);

                if (!$current || $current['status'] !== 'pending') {
                    CLI::write("MESSAGE_ID={$id} | STATUS=SKIP");
                    continue;
                }

                $recipient = $db->query("
                    SELECT
                        s.id AS siswa_id,
                        s.nama_siswa,
                        p.wa_ortu
                    FROM siswa s
                    LEFT JOIN parents p
                        ON p.id = s.ortu_id
                    WHERE s.users_id = ?
                    LIMIT 1
                ", [$current['users_id']])->getRowArray();

                if (!$recipient || empty($recipient['wa_ortu'])) {
                    $messageModel->update($id, [
                        'status'        => 'failed',
                        'error_message' => 'WA_NOT_FOUND'
                    ]);

                    CLI::write(
                        "MESSAGE_ID={$id} | STATUS=FAILED | REASON=WA_NOT_FOUND"
                    );

                    continue;
                }

                [$sent, $errorMessage] = $sender->send(
                    $recipient['wa_ortu'],
                    $current['isi_pesan']
                );

                $messageModel->update($id, [
                      'status'        => $sent ? 'sent' : 'failed',
                      'error_message' => $sent ? null : ($errorMessage ?: 'UNKNOWN_ERROR')
                  ]);

                CLI::write(
                    "MESSAGE_ID={$id} | STATUS=" .
                    ($sent ? 'SENT' : 'FAILED') .
                    ($errorMessage ? ' | ERROR=' . $errorMessage : '')
                );
            } finally {
                $db->query(
                    'SELECT RELEASE_LOCK(?) AS released',
                    [$lockName]
                );
            }
        }

        return EXIT_SUCCESS;
    }
}

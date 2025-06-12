<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeeDocument;

class NotifyExpiringDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-expiring-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invia notifiche ai dipendenti per i documenti in scadenza entro 7 giorni';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $log = Log::channel('notification');
        $log->info('[NotifyExpiringDocuments] Avvio notifica documenti in scadenza');

        $coming = Carbon::now()->addDays(7);
        $now = Carbon::now();

        $docs = EmployeeDocument::with('employee', 'document')
            ->whereDate('expiration_date', '<=', $coming)
            ->whereDate('expiration_date', '>=', $now)
            ->get();

        $log->info("[NotifyExpiringDocuments] Trovati {$docs->count()} documenti in scadenza entro 7 giorni");

        foreach ($docs as $doc) {
            try {
                if ($doc->employee && $doc->employee->email) {
                    $doc->employee->notify(new ExpiringDocumentNotification($doc));
                    $log->info("[NotifyExpiringDocuments] Notifica inviata a {$doc->employee->email} per il documento {$doc->document->name}");
                } else {
                    $log->warning("[NotifyExpiringDocuments] Impossibile notificare documento ID {$doc->id}: dipendente non valido o senza email.");
                }
            } catch (\Exception $e) {
                $log->error("[NotifyExpiringDocuments] Errore durante la notifica per il documento ID {$doc->id}: " . $e->getMessage());
            }
        }

        $log->info('[NotifyExpiringDocuments] Notifiche completate');
    }
}

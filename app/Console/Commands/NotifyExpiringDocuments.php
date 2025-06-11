<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coming = Carbon::now()->addDays(7);
        $docs = EmployeeDocument::with('employee','document')
                ->whereDate('expiration_date', '<=', $coming)
                ->whereDate('expiration_date', '>=', Carbon::now())
                ->get();

        foreach ($docs as $doc) {
            $doc->employee->notify(new ExpiringDocumentNotification($doc));
        }
    }
}

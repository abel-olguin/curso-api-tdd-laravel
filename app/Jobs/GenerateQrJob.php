<?php

namespace App\Jobs;

use App\Models\Menu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Menu $menu)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url      = config('app.frontDomain') .'/'. $this->menu->id;
        $qr       = QrCode::format('png')->size(500)->margin(10)->generate($url);
        $filename = 'qr/' . uniqid($this->menu->id . '_') . '.png';
        Storage::disk('public')->put($filename, $qr);

        $this->menu->update([
            'qr' => $filename,
        ]);
    }
}

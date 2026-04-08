<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\UpdateExpiredOffers::class,  // ✅ أضف هذا السطر
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ✅ جدولة تحديث العروض المنتهية يومياً
        $schedule->command('offers:update-expired')->daily();
        
        // يمكنك تغيير الوقت إلى كل ساعة
        // $schedule->command('offers:update-expired')->hourly();
        
        // أو كل دقيقة للتجربة
        // $schedule->command('offers:update-expired')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
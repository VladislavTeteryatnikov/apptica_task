<?php

namespace App\Console\Commands;

use App\Services\AppticaDataService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchLastMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-last-month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загружает данные при инциализации приложения за последний месяц';

    /**
     * Execute the console command.
     */
    public function handle(AppticaDataService $service)
    {
        $to = Carbon::yesterday();
        $from = $to->copy()->subDays(29);

        $this->info("Загрузка данных за последний месяц: с {$from->format('d.m.Y')} по {$to->format('d.m.Y')}");


        if ($service->fetchAndSave($from, $to)) {
            $this->info("Данные успешно загружены");
            return 0;
        } else {
            $this->error("Ошибка загрузки");
            return 1;
        }
    }
}

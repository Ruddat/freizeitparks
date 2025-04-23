<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AmusementParkService;

class ImportAmusementParks extends Command
{
    protected $signature = 'parks:import {--nologo : Importiere Parks ohne Bilder}';
    protected $description = 'Import amusement parks from Queue-Times API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(AmusementParkService $service)
    {
        $this->info('Importing amusement parks...');
        try {
            $withImages = !$this->option('nologo');
            $service->importParksToDatabase($withImages);
            $this->info('Amusement parks imported successfully!');
        } catch (\Exception $e) {
            $this->error("Failed to import parks: {$e->getMessage()}");
        }
    }
}

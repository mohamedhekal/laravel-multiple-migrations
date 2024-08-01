<?php

namespace Noouh\MultipleMigrations\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Exception;

class MigrateMultiplePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:multiple {paths*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations from multiple paths';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $paths = $this->argument('paths');

        foreach ($paths as $path) {
            $this->info("Running migrations in: $path");

            $migrationFiles = $this->getMigrationFiles($path);

            foreach ($migrationFiles as $file) {
                $this->info("Running migration: $file");
                try {
                    $this->call('migrate', [
                        '--path' => $file,
                    ]);
                } catch (Exception $e) {
                    $this->error("Failed to run migration: $file. Error: " . $e->getMessage());
                }
            }
        }

        return 0;
    }

    /**
     * Get all migration files from the specified directory.
     *
     * @param string $path
     * @return array
     */
    protected function getMigrationFiles($path)
    {
        $files = [];

        if (File::isDirectory($path)) {
            $files = File::allFiles($path);
        }

        return array_map(function ($file) use ($path) {
            return $path . '/' . $file->getFilename();
        }, $files);
    }
}

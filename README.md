# Laravel Multiple Migrations

A Laravel package to run migrations from multiple paths.

## Installation

```bash
composer require noouh/laravel-multiple-migrations
```

## Usage

```bash
php artisan migrate:multiple database/migrations
```

## License

MIT



To ensure that the command skips any migrations that fail or if the table already exists, you can wrap the migration call in a try-catch block. Here's how you can modify the command class to handle this:

### Updated Command Class

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
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
```

### Explanation

- **Try-Catch Block:** Each migration is wrapped in a try-catch block to handle any exceptions that occur during the migration. If an exception is caught, an error message is logged, and the command proceeds to the next migration file.
- **Error Handling:** If a table already exists or any other error occurs, it will be caught and skipped without stopping the execution of the remaining migrations.

### Register the Command

Ensure the command is registered in the `app/Console/Kernel.php` file:

```php
protected $commands = [
    \App\Console\Commands\MigrateMultiplePaths::class,
];
```

### Running the Command

You can run the custom command and pass the path to the extracted migrations:

```bash
php artisan migrate:multiple /mnt/data/extracted
```

This command will detect all migration files in the specified directory, attempt to run them, and skip any that fail or if the table already exists.

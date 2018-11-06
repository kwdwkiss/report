<?php

namespace Modules\Common\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;

class MigrateDown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:down {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');

        $path = app()->databasePath() . DIRECTORY_SEPARATOR . 'migrations';

        $files = glob($path . "/*$file*.php");

        $migrator = app('migrator');
        $migrator->requireFiles($files);

        foreach ($files as $item) {
            $name = $migrator->getMigrationName($item);

            $query = \DB::table('migrations')->where('migration', $name);
            if (!$query->first()) {
                $this->line("migrations not exits $name");
                continue;
            }
            $instance = $migrator->resolve($name);
            $instance->down();
            $query->delete();

            $this->line("$name down and database migration delete");
        }

    }
}

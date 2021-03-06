<?php

namespace Modules\Common\Console;

use Illuminate\Console\Command;

class MigrateUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:up {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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

        $migrator->runPending($files, []);

        foreach ($migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
    }
}

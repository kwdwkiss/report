<?php

namespace Modules\Common\Console;

use Illuminate\Console\Command;
use Modules\Common\Entities\Admin;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AdminCreate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
        $data = [
            [
                'name' => 'admin',
                'password' => bcrypt('66f5fc2d16811a216b5fd07f2aa40232')
            ],
        ];

        foreach ($data as $item) {
            Admin::updateOrCreate($item);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            //['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            //['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}

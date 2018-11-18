<?php

namespace Modules\Common\Console;

use Illuminate\Console\Command;
use Modules\Common\Entities\Agent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AgentCreate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'agent:create';

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
                'password' => bcrypt('b33aed8f3134996703dc39f9a7c95783')
            ],
        ];

        foreach ($data as $item) {
            Agent::updateOrCreate($item);
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

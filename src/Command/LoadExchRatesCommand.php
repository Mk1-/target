<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\App\ExchRateLoader;

class LoadExchRatesCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:load-exch-rates';

    private $loader;

    public function __construct(ExchRateLoader $loader)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties.
        parent::__construct();
        $this->loader = $loader;
    }
    
    protected function configure(): void
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loader->loadExchRates();
        // return this if there was no problem running the command
        return 0;

        // or return this if some error happened during the execution
        // return 1;
    }
}

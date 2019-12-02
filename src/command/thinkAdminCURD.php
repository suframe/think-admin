<?php
declare (strict_types=1);

namespace suframe\thinkAdmin\command;

use Phinx\Db\Adapter\TablePrefixAdapter;
use suframe\thinkAdmin\command\curd\Gen;
use think\migration\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class thinkAdminCURD extends Command
{
    protected function configure(): void
    {
        // 指令配置
        $this->setName('curd')
            ->addArgument('table', Argument::REQUIRED, 'table')
            ->addOption('controller', 'c', Option::VALUE_OPTIONAL, 'controller path')
            ->addOption('app', 'a', Option::VALUE_OPTIONAL, 'app')
            ->addOption('menu', 'm', Option::VALUE_NONE, 'init menu and permission')
            ->addOption('force', 'f', Option::VALUE_NONE, 'force to new')
            ->setDescription('the thinkAdmin command');
    }

    protected function execute(Input $input, Output $output): void
    {
        $table = trim($input->getArgument('table'));
        /** @var TablePrefixAdapter $adapter */
        $adapter = $this->getAdapter();
        if ($table_prefix = $adapter->getOption('table_prefix')) {
            $table = substr($table, strlen($table_prefix));
        }
        $controller = $input->hasOption('controller') ? $input->getOption('controller') : '';
        $app = $input->hasOption('app') ? $input->getOption('app') : '';
        $gen = new Gen();
        $gen->setOutput($output);
        if ($app) {
            $gen->setApp($app);
        }
        if ($input->hasOption('menu')) {
            $gen->setMenu(true);
        }
        if ($input->hasOption('force')) {
            $gen->setForce(true);
        }
        try {
            $rs = $gen->build($adapter, $table, $controller);
            if ($rs) {
                $output->info('success');
            } else {
                $output->info('fail');
            }
        } catch (\Exception $e) {
            $output->warning($e->getMessage());
        }
    }
}

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
    protected function configure()
    {
        // 指令配置
        $this->setName('curd')
            ->addArgument('table', Argument::REQUIRED, 'table')
            ->addOption('controller', 'c', Option::VALUE_OPTIONAL, 'controller path')
            ->setDescription('the thinkAdmin command');
    }

    protected function execute(Input $input, Output $output)
    {
        $table = trim($input->getArgument('table'));
        /** @var TablePrefixAdapter $adapter */
        $adapter = $this->getAdapter();
        if ($table_prefix = $adapter->getOption('table_prefix')) {
            $table = substr($table, strlen($table_prefix));
        }
        $controller = $input->hasOption('controller') ? $input->getOption('controller') : null;
        $gen = new Gen();
        $gen->setOutput($output);
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

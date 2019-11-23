<?php
declare (strict_types=1);

namespace suframe\thinkAdmin\command;

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
        $this->setName('ta')
            ->addArgument('action', Argument::REQUIRED, 'action')
            ->addOption('table', 't', Option::VALUE_OPTIONAL, 'database table name')
            ->addOption('controller', 'c', Option::VALUE_OPTIONAL, 'controller path')
            ->setDescription('the thinkAdmin command');
    }

    protected function execute(Input $input, Output $output)
    {
        $action = trim($input->getArgument('action'));

        $method = 'action' . ucfirst($action);
        if (method_exists($this, $method)) {
            $this->$method($input, $output);
        }
    }

    /**
     * 增删改查生成
     * @param Input $input
     * @param Output $output
     * @return bool
     */
    protected function actionCurd(Input $input, Output $output)
    {
        if (!$input->hasOption('table')) {
            $output->warning('please input table name, like:  php think ta curd -t news');
            return false;
        }
        $table = $input->getOption('table');
        $controller = $input->hasOption('controller') ? $input->getOption('controller') : null;
        $gen = new Gen();
        $gen->setOutput($output);
        try{
            $rs = $gen->build($this->getAdapter(), $table, $controller);
            if($rs){
                $output->info('success');
            } else {
                $output->info('fail');
            }
        } catch (\Exception $e) {
            $output->warning($e->getMessage());
        }
    }
}

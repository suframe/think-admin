<?php
declare (strict_types=1);

namespace suframe\thinkAdmin\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class thinkAdmin extends Command
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
        if(method_exists($this, $method)){
            $this->$method($input, $output);
        }
    }

    protected function actionCurd(Input $input, Output $output)
    {
        $output->writeln('yes its me');
    }
}

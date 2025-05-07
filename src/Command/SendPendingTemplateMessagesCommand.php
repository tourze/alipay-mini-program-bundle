<?php

namespace AlipayMiniProgramBundle\Command;

use AlipayMiniProgramBundle\Service\TemplateMessageService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('* * * * *')]
#[AsCommand(
    name: 'alipay:template-message:send-pending',
    description: '发送待发送的支付宝小程序模板消息',
)]
class SendPendingTemplateMessagesCommand extends Command
{
    public function __construct(
        private readonly TemplateMessageService $templateMessageService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, '每次处理的消息数量', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = (int) $input->getOption('limit');

        $io->info(sprintf('开始处理待发送的模板消息，每次处理 %d 条', $limit));

        try {
            $this->templateMessageService->sendPendingMessages($limit);
            $io->success('模板消息发送完成');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error(sprintf('发送模板消息时发生错误：%s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}

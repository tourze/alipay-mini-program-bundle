<?php

namespace AlipayMiniProgramBundle\Command;

use AlipayMiniProgramBundle\Service\FormIdService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: self::NAME,
    description: '清理过期的formId',
)]
class CleanExpiredFormIdsCommand extends Command
{
    public const NAME = 'alipay:mini-program:clean-expired-form-ids';

    public function __construct(
        private readonly FormIdService $formIdService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->formIdService->cleanExpiredFormIds();
        $output->writeln(sprintf('清理了 %d 个过期的formId', $count));

        return Command::SUCCESS;
    }
}

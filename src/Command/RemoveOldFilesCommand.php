<?php

namespace App\Command;

use App\Service\FileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:file:remove-old-ones',
    description: 'This command remove every files which are older than the given date.'
)]
class RemoveOldFilesCommand extends Command {

    public function __construct(
        private readonly FileService $fileService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $date = new \DateTimeImmutable($input->getArgument('olderThan'));
        $removedFileCount = $this->fileService->removeOlderFiles($date);

        $output->writeln("$removedFileCount file(s) deleted");

        return Command::SUCCESS;
    }

    protected function configure(): void {
        $this->addArgument('olderThan', InputArgument::REQUIRED, 'The date than you want to delete older files.');
    }
}

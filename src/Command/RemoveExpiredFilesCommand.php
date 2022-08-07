<?php

namespace App\Command;

use App\Service\FileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:file:remove-expired-ones',
    description: 'This command remove every expired files.'
)]
class RemoveExpiredFilesCommand extends Command {

    public function __construct(
        private readonly FileService $fileService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $removedFileCount = $this->fileService->removeOldFiles();

        $output->writeln("$removedFileCount file(s) deleted");

        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use App\Service\ApiKeyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:api-key:remove', description: 'This command removes an api key.')]
class RemoveApiKeyCommand extends Command {

    protected static $defaultName = 'app:api-key:remove';

    public function __construct(private readonly ApiKeyService $apiKeyService) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument('name');

        $this->apiKeyService->removeApiKeyByName($name);

        $output->writeln("Api key removed.");

        return Command::SUCCESS;
    }

    protected function configure(): void {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name that represents the API key');
    }
}

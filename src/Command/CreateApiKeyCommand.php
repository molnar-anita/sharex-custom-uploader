<?php

namespace App\Command;

use App\Service\ApiKeyService;
use App\Service\SharexConfigFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:api-key:create',
    description: 'This command creates a new api key.'
)]
class CreateApiKeyCommand extends Command {

    protected static $defaultName = 'app:api-key:create';

    public function __construct(
        private readonly ApiKeyService           $apiKeyService,
        private readonly SharexConfigFileService $sharexConfigFileService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument('name');

        $user = $this->apiKeyService->createNewApiKey($name);
        $apiKey = $user->getApiKey();

        $output->writeln("New generated API key: {$apiKey} for {$name}");

        $urlForConfig = $this->sharexConfigFileService->generateAndSaveConfigFileAndGetUrl($user);

        $output->writeln("Download the ShareX config: $urlForConfig");

        return Command::SUCCESS;
    }

    protected function configure(): void {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name that represents the API key');
    }
}

<?php

namespace App\Command;

use App\Service\ApiKeyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateNewApiKey extends Command
{
    protected static $defaultName = 'app:create-api-key';

    public function __construct(private readonly ApiKeyService $apiKeyService) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        $user = $this->apiKeyService->createNewApiKey($name);
        $apiKey = $user->getApiKey();

        $output->writeln("New generated API key: {$apiKey} for {$name}");
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name that represents the API key')
            ->setHelp('This command creates a new api key.');
    }
}

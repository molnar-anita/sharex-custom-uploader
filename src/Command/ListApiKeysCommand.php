<?php

namespace App\Command;

use App\Entity\User;
use App\Service\ApiKeyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:api-key:list',
    description: 'This command list every registered api key.'
)]
class ListApiKeysCommand extends Command {

    public function __construct(
        private readonly ApiKeyService $apiKeyService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $table = new Table($output);
        $table
            ->setHeaders(['Name', 'API Key'])
            ->setRows(
                array_map(
                    fn(User $user) => [$user->getName(), $user->getApiKey()],
                    $this->apiKeyService->getAllApiKeys()
                )
            );
        $table->render();

        return Command::SUCCESS;
    }
}

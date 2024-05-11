<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use App\Repository\FileRepository;
use App\Repository\LocalFileStorageRepository;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SharexConfigFileService {

    public function __construct(
        private readonly FileRepository             $fileRepository,
        private readonly LocalFileStorageRepository $storage,
        private readonly UrlGeneratorInterface      $router
    ) {}

    public function generateAndSaveConfigFileAndGetUrl(User $user): string {
        $config = [
            'Version' => '14.0.1',
            'DestinationType' => 'ImageUploader, TextUploader, FileUploader',
            'RequestMethod' => 'POST',
            'RequestURL' => $this->router->generate('api.file.create', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            'Headers' => [
                'Authentication' => 'App ' . $user->getApiKey()
            ],
            'Body' => 'MultipartFormData',
            'FileFormName' => 'file',
            'URL' => '{json:downloadUrl}',
            'DeletionURL' => '{json:removeUrl}'
        ];

        $filePath = $this->storage->saveContentWithRandomName(json_encode($config, JSON_PRETTY_PRINT));

        $file = (new File())
            ->setName('custom-sharex-config.sxcu')
            ->setPath($filePath)
            ->setAccessOnce(true)
            ->setMime('application/json')
            ->setExpireIn((new DateTimeImmutable())->add(new DateInterval('PT10M')))
            ->setUser($user);

        $this->fileRepository->add($file, true);

        return $this->router->generate(
            'api.file.download.uuid',
            ['uuid' => $file->getUuid(), 'fileName' => $file->getName()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}

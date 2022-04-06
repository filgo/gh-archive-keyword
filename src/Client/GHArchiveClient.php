<?php

namespace App\Client;

use App\Dto\GHArchive\EventOutput;
use Generator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GHArchiveClient implements GHArchiveClientInterface
{
    private const ENDPOINT = '/{dateImport}.json.gz';
    private const TEMP_NAME = '/tmp/archive.json.gz';

    private HttpClientInterface $httpClient;
    private SerializerInterface $serializer;

    public function __construct(HttpClientInterface $gharchiveClient, SerializerInterface $serializer)
    {
        $this->httpClient = $gharchiveClient;
        $this->serializer = $serializer;
    }

    public function retrieveData(string $dateImport): ?Generator
    {
        $endpoint = str_replace('{dateImport}', $dateImport, self::ENDPOINT);

        $response = $this->httpClient->request('GET', $endpoint);

        file_put_contents(self::TEMP_NAME, $response->getContent());
        $sfp = gzopen(self::TEMP_NAME, 'r');

        while ($data = fgets($sfp)) {
            yield $this->serializer->deserialize($data, EventOutput::class, 'json', ['disable_type_enforcement' => true]);
        }
        gzclose($sfp);
        unlink(self::TEMP_NAME);

        return null;
    }
}

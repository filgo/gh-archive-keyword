<?php

namespace App\Client;

use Generator;

interface GHArchiveClientInterface
{
    public function retrieveData(string $dateImport): ?Generator;
}

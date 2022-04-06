<?php

declare(strict_types=1);

namespace App\Dto\GHArchive;

use Symfony\Component\Validator\Constraints as Assert;

class RepoOutput
{
    public int $id;

    public string $name;

    /**
     * @Assert\Url
     */
    public string $url;
}

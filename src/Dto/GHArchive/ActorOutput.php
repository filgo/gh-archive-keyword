<?php

declare(strict_types=1);

namespace App\Dto\GHArchive;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ActorOutput
{
    public int $id;

    /**
     * @Assert\Length(min=255)
     */
    public string $login;

    /**
     * @Assert\Url
     */
    public string $url;

    /**
     * @SerializedName("avatar_url")
     * @Assert\Url
     */
    public string $avatarUrl;
}

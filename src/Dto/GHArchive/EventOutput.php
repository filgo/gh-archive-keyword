<?php

declare(strict_types=1);

namespace App\Dto\GHArchive;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class EventOutput
{
    public int $id;

    /**
     * @Assert\Choice(callback={"App\Dto\GHArchive\EventType", "getAll"})
     */
    public string $type;

    public array $payload;

    /**
     * @SerializedName("created_at")
     */
    public \DateTimeImmutable $createAt;

    /**
     * @SerializedName("message")
     */
    public ?string $comment = null;

    public ActorOutput $actor;

    public RepoOutput $repo;
}

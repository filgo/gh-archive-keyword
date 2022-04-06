<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\GHArchive\EventType as GHArchiveEventType;
use App\Entity\EventType;

class EventTypeMapper
{
    private const MAP_GHARCHIVE_EVENT_TYPE = [
        GHArchiveEventType::COMMENT => EventType::COMMENT,
        GHArchiveEventType::COMMIT => EventType::COMMIT,
        GHArchiveEventType::PULL_REQUEST => EventType::PULL_REQUEST,
    ];

    public function mapFromGHArchiveEventType(string $type): ?string
    {
        return self::MAP_GHARCHIVE_EVENT_TYPE[$type] ?? null;
    }
}

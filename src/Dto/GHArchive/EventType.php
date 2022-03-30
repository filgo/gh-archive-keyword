<?php

declare(strict_types=1);

namespace App\Dto\GHArchive;

class EventType
{
    public const COMMIT = 'PushEvent';
    public const COMMENT = 'CommitCommentEvent';
    public const PULL_REQUEST = 'PullRequestEvent';

    public static function getAll(): array
    {
        return [
            self::COMMIT,
            self::COMMENT,
            self::PULL_REQUEST,
        ];
    }
}

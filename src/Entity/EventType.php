<?php

namespace App\Entity;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventType extends AbstractEnumType
{
    public const COMMIT = 'COM';
    public const COMMENT = 'MSG';
    public const PULL_REQUEST = 'PR';

    /**
     * @var array<string, string>
     */
    public static array $choicesForStats = [
        self::COMMIT => 'commit',
        self::COMMENT => 'comment',
        self::PULL_REQUEST => 'pullRequest',
    ];

    /**
     * @var array<string, string>
     */
    protected static array $choices = [
        self::COMMIT => 'Commit',
        self::COMMENT => 'Comment',
        self::PULL_REQUEST => 'Pull Request',
    ];
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Dto\GHArchive\ActorOutput;
use App\Dto\GHArchive\EventOutput;
use App\Dto\GHArchive\RepoOutput;
use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\Repo;
use App\Factory\EventFactory;
use App\Mapper\EventTypeMapper;
use App\Repository\ActorRepository;
use App\Repository\RepoRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @internal
 * @coversNothing
 */
class EventFactoryTest extends TestCase
{
    use ProphecyTrait;

    private EventFactory $testedObject;
    private ObjectProphecy $eventTypeMapper;
    private ObjectProphecy $actorRepository;
    private ObjectProphecy $repoRepository;

    public function setUp(): void
    {
        $this->eventTypeMapper = $this->prophesize(EventTypeMapper::class);
        $this->actorRepository = $this->prophesize(ActorRepository::class);
        $this->repoRepository = $this->prophesize(RepoRepository::class);

        $this->testedObject = new EventFactory(
            $this->eventTypeMapper->reveal(),
            $this->actorRepository->reveal(),
            $this->repoRepository->reveal(),
        );
    }

    public function testCreateFromGHArchiveEventType(): void
    {
        $eventOutput = new EventOutput();
        $eventOutput->id = 1111;
        $eventOutput->payload = [
            'key_1' => 'value_1',
            'key_2' => 'value_2',
        ];
        $eventOutput->type = 'gh_event_type';
        $eventOutput->createAt = new \DateTimeImmutable('2022-01-01 10:01:02');

        $actorOutput = new ActorOutput();
        $actorOutput->id = 2111;
        $actorOutput->login = 'login';
        $actorOutput->url = 'url';
        $actorOutput->avatarUrl = 'avatar_url';

        $repoOutput = new RepoOutput();
        $repoOutput->id = 3111;
        $repoOutput->name = 'repo_name';
        $repoOutput->url = 'repo_url';

        $eventOutput->actor = $actorOutput;
        $eventOutput->repo = $repoOutput;

        $this->eventTypeMapper->mapFromGHArchiveEventType('gh_event_type')->willReturn('COM');
        $this->actorRepository->find(2111)->willReturn(null);
        $this->repoRepository->find(3111)->willReturn(null);

        $result = $this->testedObject->createFromGHArchiveEventType($eventOutput);

        $actor = new Actor(2111, 'login', 'url', 'avatar_url');
        $repo = new Repo(3111, 'repo_name', 'repo_url');
        $expectedEvent = new Event(
            1111,
            'COM',
            $actor,
            $repo,
            [
                'key_1' => 'value_1',
                'key_2' => 'value_2',
            ],
            new \DateTimeImmutable('2022-01-01 10:01:02')
        );

        $this->assertEquals($expectedEvent, $result);
    }
}

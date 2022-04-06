<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\GHArchive\EventOutput;
use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\Repo;
use App\Mapper\EventTypeMapper;
use App\Repository\ActorRepository;
use App\Repository\RepoRepository;

class EventFactory
{
    private EventTypeMapper $eventTypeMapper;
    private ActorRepository $actorRepository;
    private RepoRepository $repoRepository;

    public function __construct(EventTypeMapper $eventTypeMapper, ActorRepository $actorRepository, RepoRepository $repoRepository)
    {
        $this->eventTypeMapper = $eventTypeMapper;
        $this->actorRepository = $actorRepository;
        $this->repoRepository = $repoRepository;
    }

    public function createFromGHArchiveEventType(EventOutput $gHArchiveEvent): Event
    {
        $actorOutput = $gHArchiveEvent->actor;
        $repoOutput = $gHArchiveEvent->repo;
        $type = $this->eventTypeMapper->mapFromGHArchiveEventType($gHArchiveEvent->type);

        if (null === $type) {
            throw new \Exception(sprintf('%s event type not found', $gHArchiveEvent->type));
        }

        $actor = $this->actorRepository->find($actorOutput->id);

        if (null === $actor) {
            $actor = new Actor($actorOutput->id, $actorOutput->login, $actorOutput->url, $actorOutput->avatarUrl);
        }

        $repo = $this->repoRepository->find($repoOutput->id);

        if (null === $repo) {
            $repo = new Repo($repoOutput->id, $repoOutput->name, $repoOutput->url);
        }

        return new Event($gHArchiveEvent->id, $type, $actor, $repo, $gHArchiveEvent->payload, $gHArchiveEvent->createAt);
    }
}

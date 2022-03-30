<?php

declare(strict_types=1);

namespace App\Command;

use App\Client\GHArchiveClientInterface;
use App\Dto\GHArchive\EventOutput;
use App\Factory\EventFactory;
use App\Repository\ReadEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
class ImportGitHubEventsCommand extends Command
{
    protected static $defaultName = 'app:import-github-events';

    private GHArchiveClientInterface $gHArchiveClient;
    private ReadEventRepository $readEventRepository;
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;
    private EventFactory $eventFactory;

    public function __construct(
        GHArchiveClientInterface $gHArchiveClient,
        ReadEventRepository $readEventRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        EventFactory $eventFactory
    ) {
        $this->gHArchiveClient = $gHArchiveClient;
        $this->readEventRepository = $readEventRepository;
        $this->validator = $validator;
        $this->em = $em;
        $this->eventFactory = $eventFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GH events')
            ->addArgument('import_date', InputArgument::REQUIRED, 'Date of import ? (Example 2022-01-01)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Import GH Archive');

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        foreach ($io->progressIterate(range(0, 23)) as $hour) {
            $enventsOutput = $this->gHArchiveClient->retrieveData($input->getArgument('import_date').'-'.$hour);

            if (null !== $enventsOutput) {
                /** @var EventOutput $eventOutput */
                foreach ($enventsOutput as $eventOutput) {
                    $errors = $this->validator->validate($eventOutput);

                    if (0 === \count($errors) && false === $this->readEventRepository->exist($eventOutput->id)) {
                        $event = $this->eventFactory->createFromGHArchiveEventType($eventOutput);
                        $this->em->persist($event);
                        $this->em->flush();
                        $this->em->clear();
                    }
                }
            }
        }

        $io->progressFinish();

        return 1;
    }
}

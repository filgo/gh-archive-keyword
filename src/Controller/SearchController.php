<?php

namespace App\Controller;

use App\Dto\SearchInput;
use App\Entity\EventType;
use App\Repository\ReadEventRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SearchController
{
    private ReadEventRepository $repository;
    private DenormalizerInterface $denormalizer;

    public function __construct(
        ReadEventRepository $repository,
        DenormalizerInterface $denormalizer
    ) {
        $this->repository = $repository;
        $this->denormalizer = $denormalizer;
    }

    /**
     * @Route(path="/api/search", name="api_search", methods={"GET"})
     */
    public function searchCommits(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $searchInput = $this->denormalizer->denormalize($request->query->all(), SearchInput::class);

        $errors = $validator->validate($searchInput);

        if (\count($errors) > 0) {
            return new JsonResponse(
                ['message' => $errors->get(0)->getPropertyPath().': '.$errors->get(0)->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $countByType = $this->repository->countByType($searchInput);

        $data = [
            'meta' => [
                'totalEvents' => $this->repository->countAll($searchInput),
                'totalPullRequests' => $countByType[EventType::PULL_REQUEST] ?? 0,
                'totalCommits' => $countByType[EventType::COMMIT] ?? 0,
                'totalComments' => $countByType[EventType::COMMENT] ?? 0,
            ],
            'data' => [
                'events' => $this->repository->getLatest($searchInput),
                'stats' => $this->repository->statsByTypePerHour($searchInput),
            ],
        ];

        return new JsonResponse($data);
    }
}

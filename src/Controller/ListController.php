<?php

namespace BestWishes\Controller;

use BestWishes\Entity\GiftList;
use BestWishes\Manager\ListEventManager;
use BestWishes\Pdf\GotenbergRenderer;
use BestWishes\Repository\GiftListRepository;
use BestWishes\Security\Voter\GiftListVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: 'list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly GiftListRepository     $giftListRepository,
        private readonly ListEventManager       $listEventManager,
        private readonly GotenbergRenderer      $pdf
    ) {
    }

    #[Route(path: '/', name: 'list_index')]
    public function index(): Response
    {
        $lists = $this->giftListRepository->findAll();

        return $this->render('list/index.html.twig', compact('lists'));
    }

    #[Route(path: '/{id}', name: 'list_show', requirements: ['id' => '\d+'])]
    public function show(Request $request): Response
    {
        $list = $this->loadVisibleList($request->attributes->getInt('id'));
        $nextEventData = $this->listEventManager->getNearestEventData($list);

        return $this->render(
            'list/show.html.twig',
            compact('list', 'nextEventData'),
            new Response(headers: ['Cache-Control' => 'private, no-store'])
        );
    }

    #[Route(path: '/export/{id}', name: 'list_export_pdf', requirements: ['id' => '\d+'])]
    public function pdfExport(Request $request): Response
    {
        $list = $this->loadVisibleList($request->attributes->getInt('id'));
        $nextEventData = $this->listEventManager->getNearestEventData($list);

        $pdf = $this->pdf->render('list/export.pdf.twig', compact('list', 'nextEventData'));

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                \sprintf('list_%s.pdf', $list->getSlug())
            ),
        ]);
    }

    private function loadVisibleList(int $id): GiftList
    {
        $list = $this->giftListRepository->find($id);
        if (!$list) {
            throw $this->createNotFoundException();
        }

        $visibleList = $this->isGranted(GiftListVoter::VIEW_SURPRISES, $list)
            ? $this->giftListRepository->findFullById($id)
            : $this->giftListRepository->findFullSurpriseExcludedById($id);

        return $visibleList ?? throw $this->createNotFoundException();
    }
}

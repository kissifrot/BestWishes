<?php

namespace BestWishes\Controller;

use BestWishes\Manager\ListEventManager;
use BestWishes\Repository\GiftListRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly GiftListRepository     $giftListRepository,
        private readonly ListEventManager       $listEventManager,
        private readonly Pdf                    $pdf
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
        $id = $request->attributes->getInt('id');
        $list = $this->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ? $this->giftListRepository->findFullById($id)
            : $this->giftListRepository->findFullSurpriseExcludedById($id)
        ;
        if (!$list) {
            throw $this->createNotFoundException();
        }
        $nextEventData = $this->listEventManager->getNearestEventData($list);

        return $this->render('list/show.html.twig', compact('list', 'nextEventData'));
    }

    #[Route(path: '/export/{id}', name: 'list_export_pdf', requirements: ['id' => '\d+'])]
    public function pdfExport(Request $request): PdfResponse
    {
        $id = $request->attributes->getInt('id');
        $list = $this->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ? $this->giftListRepository->findFullById($id)
            : $this->giftListRepository->findFullSurpriseExcludedById($id)
        ;
        if (!$list) {
            throw $this->createNotFoundException();
        }
        $nextEventData = $this->listEventManager->getNearestEventData($list);

        $html = $this->renderView('list/export.pdf.twig', compact('list', 'nextEventData'));

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            sprintf('list_%s.pdf', $list->getSlug())
        );
    }
}

<?php

namespace BestWishes\Controller;

use BestWishes\Entity\GiftList;
use BestWishes\Manager\ListEventManager;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ListController
 * @Route("list")
 */
class ListController extends AbstractController
{
    private $listEventManager;
    private $pdf;

    public function __construct(ListEventManager $listEventManager, Pdf $pdf)
    {
        $this->listEventManager = $listEventManager;
        $this->pdf = $pdf;
    }

    /**
     * @Route("/", name="list_index")
     */
    public function index(): Response
    {
        $lists = $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findAll();

        return $this->render('list/index.html.twig', compact('lists'));
    }

    /**
     * @Route("/{id}", name="list_show", requirements={"id": "\d+"})
     *
     * @return Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request)
    {
        $id = $request->attributes->getInt('id');
        $list = $this->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ? $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findFullById($id)
            : $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findFullSurpriseExcludedById($id)
        ;
        if (!$list) {
            throw $this->createNotFoundException();
        }
        $nextEventData = $this->listEventManager->getNearestEventData($list);

        return $this->render('list/show.html.twig', compact('list', 'nextEventData'));
    }

    /**
     * @Route("/export/{id}", name="list_export_pdf", requirements={"id": "\d+"})
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function pdfExport(Request $request): PdfResponse
    {
        $id = $request->attributes->getInt('id');
        $list = $this->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ? $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findFullById($id)
            : $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findFullSurpriseExcludedById($id)
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

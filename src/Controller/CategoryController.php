<?php

namespace BestWishes\Controller;

use BestWishes\Entity\Category;
use BestWishes\Entity\GiftList;
use BestWishes\Event\CategoryCreatedEvent;
use BestWishes\Event\CategoryDeletedEvent;
use BestWishes\Event\CategoryEditedEvent;
use BestWishes\Form\Type\CategoryType;
use BestWishes\Manager\SecurityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController
 * @Route("cat")
 */
class CategoryController extends AbstractController
{
    private $securityManager;
    private $translator;
    private $eventDispatcher;

    public function __construct(SecurityManager $securityManager, TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher)
    {
        $this->securityManager = $securityManager;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @Route("/{id}", name="category_show", requirements={"id": "\d+"}, methods={"GET"})
     *
     * @return Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request)
    {
        $id = $request->attributes->getInt('id');
        $category = $this->loadCategory($id);
        if (!$category) {
            throw $this->createNotFoundException();
        }
        $deleteForm = $this->createDeleteForm($category)->createView();

        return $this->render('category/show.html.twig', compact('category', 'deleteForm'));
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function loadCategory(int $id): ?Category
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->getDoctrine()->getManager()->getRepository(Category::class)->findFullSurpriseExcludedById($id);
        }

        // Load the list to check access on
        $categoryGiftList = $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findByCategoryId($id);
        if (!$categoryGiftList) {
            return null;
        }

        if ($this->isGranted('OWNER', $categoryGiftList)) {
            return $this->getDoctrine()->getManager()->getRepository(Category::class)->findFullSurpriseExcludedById($id);
        }

        return $this->getDoctrine()->getManager()->getRepository(Category::class)->findFullById($id);
    }

    /**
     * @param Request  $request
     * @param GiftList $list
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/create/{listId}", name="category_create", requirements={"listId": "\d+"}, methods={"GET", "POST"})
     * @ParamConverter("list", options={"id" = "listId"})
     *
     */
    public function create(Request $request, GiftList $list): Response
    {
        $this->securityManager->checkAccess(['OWNER', 'EDIT'], $list);

        $category = new Category();
        $category->setList($list);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->eventDispatcher->dispatch( new CategoryCreatedEvent($category), CategoryCreatedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('category.message.created', ['%categoryName%' => $category->getName()]));

            return $this->redirectToRoute('list_show', ['id' => $list->getId()]);
        }

        return $this->render('category/create.html.twig', ['list' => $list, 'form' => $form->createView()]);
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("{id}/edit", name="category_edit", requirements={"id": "\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $this->securityManager->checkAccess(['OWNER', 'EDIT'], $category->getList());

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->eventDispatcher->dispatch(new CategoryEditedEvent($category), CategoryEditedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('category.message.updated', ['%categoryName%' => $category->getName()]));

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Category    $category
     * @Route("/{id}/delete", name="category_delete", requirements={"id": "\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function delete(Request $request, Category $category): Response
    {
        $this->securityManager->checkAccess(['OWNER', 'EDIT'], $category->getList());

        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $deletedCategory = clone $category;
            $em->remove($category);
            $em->flush();

            $this->eventDispatcher->dispatch(new CategoryDeletedEvent($deletedCategory), CategoryDeletedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('category.message.deleted', ['%categoryName%' => $category->getName()]));
        }

        return $this->redirectToRoute('list_show', ['id' => $category->getList()->getId()]);
    }

    /**
     * Creates a form for deletion
     *
     * @param Category   $category
     *
     * @return FormInterface Delete form
     */
    private function createDeleteForm(Category $category): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', ['id' => $category->getId()]))
            ->setMethod('POST')
            ->getForm();
    }
}

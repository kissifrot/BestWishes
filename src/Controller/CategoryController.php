<?php

namespace BestWishes\Controller;

use BestWishes\Entity\Category;
use BestWishes\Entity\GiftList;
use BestWishes\Event\CategoryCreatedEvent;
use BestWishes\Event\CategoryDeletedEvent;
use BestWishes\Event\CategoryEditedEvent;
use BestWishes\Form\Type\CategoryType;
use BestWishes\Manager\SecurityManager;
use BestWishes\Repository\CategoryRepository;
use BestWishes\Repository\GiftListRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: 'cat')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository       $categoryRepository,
        private readonly GiftListRepository       $giftListRepository,
        private readonly SecurityManager          $securityManager,
        private readonly TranslatorInterface      $translator,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    #[Route(path: '/{id}', name: 'category_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request): Response
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
     * @throws NonUniqueResultException
     */
    private function loadCategory(int $id): ?Category
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->categoryRepository->findFullSurpriseExcludedById($id);
        }

        // Load the list to check access on
        $categoryGiftList = $this->giftListRepository->findByCategoryId($id);
        if (!$categoryGiftList) {
            return null;
        }

        if ($this->isGranted('OWNER', $categoryGiftList)) {
            return $this->categoryRepository->findFullSurpriseExcludedById($id);
        }

        return $this->categoryRepository->findFullById($id);
    }

    #[Route(path: '/create/{listId}', name: 'category_create', requirements: ['listId' => '\d+'], methods: ['GET', 'POST'])]
    public function create(Request $request, #[MapEntity(expr: 'repository.find(listId)')] GiftList $list): Response
    {
        $this->securityManager->checkAccess(['OWNER', 'EDIT'], $list);

        $category = new Category();
        $category->setList($list);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->save($category, flush: true);

            $this->eventDispatcher->dispatch(new CategoryCreatedEvent($category), CategoryCreatedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('category.message.created', ['%categoryName%' => $category->getName()]));

            return $this->redirectToRoute('list_show', ['id' => $list->getId()]);
        }

        return $this->render('category/create.html.twig', ['list' => $list, 'form' => $form->createView()]);
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '{id}/edit', name: 'category_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        $this->securityManager->checkAccess(['OWNER', 'EDIT'], $category->getList());

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->save($category, flush: true);

            $this->eventDispatcher->dispatch(new CategoryEditedEvent($category), CategoryEditedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('category.message.updated', ['%categoryName%' => $category->getName()]));

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '{id}/delete', name: 'category_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Category $category): Response
    {
        $this->securityManager->checkAccess(['OWNER', 'EDIT'], $category->getList());

        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deletedCategory = clone $category;
            $this->categoryRepository->remove($category, flush: true);

            $this->eventDispatcher->dispatch(new CategoryDeletedEvent($deletedCategory), CategoryDeletedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('category.message.deleted', ['%categoryName%' => $category->getName()]));
        }

        return $this->redirectToRoute('list_show', ['id' => $category->getList()->getId()]);
    }

    /**
     * Creates a form for deletion
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

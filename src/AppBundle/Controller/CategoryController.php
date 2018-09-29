<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use AppBundle\Entity\GiftList;
use AppBundle\Form\Type\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @Route("cat")
 */
class CategoryController extends BwController
{
    /**
     * @param Request $request
     * @Route("/{id}", name="category_show", requirements={"id": "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request)
    {
        $id = $request->get('id');

        $category = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->findFullById($id);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        $deleteForm = $this->createDeleteForm($category)->createView();

        return $this->render('AppBundle:category:show.html.twig', compact('category', 'deleteForm'));
    }

    /**
     * @param Request  $request
     * @param GiftList $list
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/create/{listId}", name="category_create", requirements={"listId": "\d+"})
     * @ParamConverter("list", class="AppBundle:GiftList", options={"id" = "listId"})
     * @Method({"GET", "POST"})
     *
     */
    public function createAction(Request $request, GiftList $list): \Symfony\Component\HttpFoundation\Response
    {
        // Access control
        $this->checkAccess(['OWNER', 'EDIT'], $list);

        $category = new Category();
        $category->setList($list);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('category.message.updated', ['%categoryName%' => $category->getName()]));

            return $this->redirectToRoute('list_show', ['id' => $list->getId()]);
        }

        return $this->render('AppBundle:category:create.html.twig', ['list' => $list, 'form' => $form->createView()]);
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("{id}/edit", name="category_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category): \Symfony\Component\HttpFoundation\Response
    {
        // Access control
        $this->checkAccess(['OWNER', 'EDIT'], $category->getList());

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('category.message.updated', ['%categoryName%' => $category->getName()]));

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('AppBundle:category:edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Category    $category
     * @Route("/{id}", name="category_delete", requirements={"id": "\d+"})
     * @Method({"DELETE"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Category $category): \Symfony\Component\HttpFoundation\Response
    {
        // Access control
        $this->checkAccess(['OWNER', 'EDIT'], $category->getList());

        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            $this->addFlash('notice', $this->get('translator')->trans('category.message.deleted', ['%categoryName%' => $category->getName()]));
        }

        return $this->redirectToRoute('list_show', ['id' => $category->getList()->getId()]);
    }

    /**
     * Creates a form for deletion
     *
     * @param Category   $category
     *
     * @return \Symfony\Component\Form\FormInterface Delete form
     */
    private function createDeleteForm(Category $category): \Symfony\Component\Form\FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', ['id' => $category->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}

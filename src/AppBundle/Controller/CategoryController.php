<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use AppBundle\Form\Type\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 */
class CategoryController extends Controller
{
    /**
     * @param Request $request
     * @Route("/cat/{id}", name="category_show", requirements={"id": "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request)
    {
        $id = $request->get('id');

        $category = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Category')->findFullById($id);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        return $this->render('AppBundle:category:show.html.twig', compact('category'));
    }

    /**
     * @param Request $request
     * @Route("/cat/create/{listId}", name="category_create", requirements={"listId": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $listId = $request->get('listId');
        $relatedList = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:GiftList')->find($listId);
        if (!$relatedList) {
            throw $this->createNotFoundException();
        }

        $category = new Category();
        $category->setList($relatedList);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', sprintf('Category ""%s added',$category->getName()));

            return $this->redirectToRoute('list_show', ['id' => $relatedList->getId()]);
        }

        return $this->render('AppBundle:category:create.html.twig', ['form' => $form->createView()]);
    }
}

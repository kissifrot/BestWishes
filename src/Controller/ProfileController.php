<?php

namespace BestWishes\Controller;

use BestWishes\Form\ChangePasswordFormType;
use BestWishes\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/profile")
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/change-password", name="user_profile_change_password")
     */
    public function changePassword(Request $request, UserManager $userManager, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updatePassword($user, $form->get('plainPassword')->getData());
            $this->entityManager->flush();

            $this->addFlash('success', $translator->trans('change_password.flash.success'));

            return $this->redirectToRoute('user_home');
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

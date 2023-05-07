<?php

namespace BestWishes\Controller;

use BestWishes\Entity\User;
use BestWishes\Form\ChangePasswordFormType;
use BestWishes\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/change-password', name: 'user_profile_change_password')]
    public function changePassword(Request $request, UserManager $userManager, TranslatorInterface $translator): Response
    {
        /** @var User $user */
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

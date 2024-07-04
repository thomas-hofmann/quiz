<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController {
	#[Route('/admin-dashboard', name: 'admin-dashboard')]
	public function AdminAction(Request $request, EntityManagerInterface $entityManager): Response {
        $userRepository = $entityManager->getRepository(User::class);

        $users = $userRepository->findAll(['id' => 'DESC']);
		return $this->render('admin/admin-dashboard.html.twig', [
            'users' => $users,
		]);
	}

    #[Route('/admin-dashboard/create-user', name: 'create-user')]
    public function newUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response {
        if ($request->get('username') && $request->get('password')) {
            $user = new User();

            $user->setUsername($request->get('username'));
            $user->setPassword($passwordHasher->hashPassword($user, $request->get('password')));
            $user->setRoles(["ROLE_USER"]);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Nutzer angelegt!'
            );
        }
    
        return $this->redirectToRoute('admin-dashboard');
    }

    #[Route('/delete-user/{id}', name: 'delete-user', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('danger', 'Benutzer erfolgreich gelöscht.');

        return $this->redirectToRoute('admin-dashboard');
    }

    #[Route('/admin-dashboard/change-password/{id}', name: 'change-password')]
    public function changePassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response {
        if ($request->get('new_password')) {
            $user->setPassword($passwordHasher->hashPassword($user, $request->get('new_password')));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Passwort geändert!'
            );
        }
    
        return $this->redirectToRoute('admin-dashboard');
    }
}
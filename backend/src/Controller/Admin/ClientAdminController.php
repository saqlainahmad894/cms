<?php

namespace App\Controller\Admin;

use App\Entity\ClientProfile;
use App\Entity\User;
use App\Repository\ClientProfileRepository;
use App\Repository\ClientCaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/clients')]
#[IsGranted('ROLE_ADMIN')]
class ClientAdminController extends AbstractController
{
    #[Route('', name: 'admin_client_list', methods: ['GET'])]
    public function list(ClientProfileRepository $repo): JsonResponse
    {
        return $this->json([
            'clients' => $repo->findAllWithUserEmail()
        ]);
    }

    #[Route('/{id}', name: 'admin_client_view', methods: ['GET'])]
    public function view(ClientProfile $client): JsonResponse
    {
        return $this->json([
            'id' => $client->getId(),
            'fullName' => $client->getFullName(),
            'phoneNumber' => $client->getPhoneNumber(),
            'address' => $client->getAddress(),
            'profilePicture' => $client->getProfilePicture(),
            'email' => $client->getUser()->getEmail()
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_client_delete', methods: ['DELETE'])]
    public function delete(ClientProfile $client, ClientCaseRepository $caseRepo, EntityManagerInterface $em): JsonResponse
    {
        $openCases = $caseRepo->findBy([
            'user' => $client->getUser(),
            'status' => ['Assigned', 'In Progress']
        ]);

        if (count($openCases) > 0) {
            return $this->json(['message' => 'Cannot delete client with open cases.'], Response::HTTP_FORBIDDEN);
        }

        $em->remove($client);
        $em->flush();

        return $this->json(['message' => 'Client deleted successfully.']);
    }

    #[Route('/add', name: 'admin_client_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword(password_hash('default123', PASSWORD_BCRYPT)); // default password

            // Fetch ROLE_CLIENT from Role table
            $role = $em->getRepository(\App\Entity\Role::class)->findOneBy(['name' => 'ROLE_CLIENT']);
            if (!$role) {
                return $this->json(['message' => 'ROLE_CLIENT not found in roles table.'], 500);
            }

            $user->addRole($role);
            $em->persist($user);
        }

        $client = new ClientProfile();
        $client->setUser($user)
            ->setFullName($data['fullName'])
            ->setPhoneNumber($data['phoneNumber'])
            ->setAddress($data['address'])
            ->setProfilePicture($data['profilePicture']);

        $em->persist($client);
        $em->flush();

        return $this->json(['message' => 'Client added successfully']);
    }

    #[Route('/{id}/edit', name: 'admin_client_edit', methods: ['PUT'])]
    public function edit($id, Request $request, ClientProfileRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $client = $repo->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client not found'], 404);
        }

        $client->setFullName($data['fullName'])
            ->setPhoneNumber($data['phoneNumber'])
            ->setAddress($data['address'])
            ->setProfilePicture($data['profilePicture']);

        $em->flush();

        return $this->json(['message' => 'Client updated successfully']);
    }
    
}

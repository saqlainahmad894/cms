<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ClientCaseRepository;
use App\Repository\ClientProfileRepository;
use App\Repository\MessageThreadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    //#[Route('/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function index(
        UserRepository $userRepo,
        ClientCaseRepository $caseRepo,
        MessageThreadRepository $messageRepo,
        ClientProfileRepository $profileRepo
    ): JsonResponse {
        $clientCount = $userRepo->countByRole('ROLE_CLIENT');
        $totalCases = $caseRepo->count([]);
        $caseStatusCount = $caseRepo->countGroupedByStatus();
        $recentMessages = $messageRepo->findRecentMessagesWithSenders(5);
        $clientProfiles = $profileRepo->findAllClientProfiles();

        return $this->json([
            'clients' => $clientCount,
            'cases' => $totalCases,
            'status_breakdown' => $caseStatusCount,
            'recent_messages' => $recentMessages,
            'client_profiles' => $clientProfiles,
        ]);
    }
}

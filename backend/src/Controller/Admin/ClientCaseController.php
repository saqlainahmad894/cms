<?php
// src/Controller/Admin/ClientCaseController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\CaseDocument;
use App\Entity\ClientCase;
use App\Repository\CaseDocumentRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClientCaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/client-cases')]
#[IsGranted('ROLE_ADMIN')]
class ClientCaseController extends AbstractController
{
    #[Route('/{userId}', name: 'admin_client_case_list', methods: ['GET'])]
    public function list(int $userId, ClientCaseRepository $repo): JsonResponse
    {
        $cases = $repo->findBy(['user' => $userId]);

        $data = array_map(fn($case) => [
            'id' => $case->getId(),
            'title' => $case->getTitle(),
            'description' => $case->getDescription(),
            'status' => $case->getStatus(),
            'createdAt' => $case->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $case->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'clientName' => $case->getUser()->getClientProfile()?->getFullName() ?? $case->getUser()->getEmail()
        ], $cases);

        return $this->json($data);
    }

    #[Route('/view/{id}', name: 'admin_client_case_view', methods: ['GET'])]
    public function view(int $id, ClientCaseRepository $repo): JsonResponse
    {
        $case = $repo->find($id);
        if (!$case) {
            return $this->json(['message' => 'Case not found'], 404);
        }

        return $this->json([
            'id' => $case->getId(),
            'title' => $case->getTitle(),
            'description' => $case->getDescription(),
            'status' => $case->getStatus(),
            'createdAt' => $case->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $case->getUpdatedAt()?->format('Y-m-d H:i:s')
        ]);
    }
    #[Route('/add', name: 'admin_client_case_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $em->getRepository(User::class)->find($data['userId']);

        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $case = new \App\Entity\ClientCase();
        $case->setUser($user)
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setCreatedAt(new \DateTime());

        $em->persist($case);
        $em->flush();

        return $this->json(['message' => 'Case added successfully']);
    }
    #[Route('/{id}/edit', name: 'admin_client_case_edit', methods: ['PUT'])]
    public function edit(int $id, Request $request, ClientCaseRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $case = $repo->find($id);

        if (!$case) {
            return $this->json(['message' => 'Case not found'], 404);
        }

        $case->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setUpdatedAt(new \DateTime());

        $em->flush();

        return $this->json(['message' => 'Case updated successfully']);
    }
    #[Route('/{caseId}/upload-document', name: 'admin_client_case_upload_document', methods: ['POST'])]
    public function uploadDocument(int $caseId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $case = $em->getRepository(ClientCase::class)->find($caseId);
        if (!$case) {
            return $this->json(['message' => 'Case not found'], 404);
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if (!$file) {
            return $this->json(['message' => 'No file provided'], 400);
        }

        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move('uploads/case_docs', $filename);

        $document = new CaseDocument();
        $document->setCase($case)
            ->setFileName($file->getClientOriginalName())
            ->setFileUrl($request->getSchemeAndHttpHost() . '/uploads/case_docs/' . $filename)
            ->setFileType($file->getClientMimeType());

        $em->persist($document);
        $em->flush();

        return $this->json(['message' => 'Document uploaded successfully']);
    }
    #[Route('/{caseId}/documents', name: 'admin_client_case_documents', methods: ['GET'])]
    public function listDocuments(int $caseId, CaseDocumentRepository $docRepo, EntityManagerInterface $em): JsonResponse
    {
        $case = $em->getRepository(ClientCase::class)->find($caseId);
        if (!$case) {
            return $this->json(['message' => 'Case not found'], 404);
        }

        $documents = $docRepo->findBy(['case' => $case]);

        $data = array_map(fn($doc) => [
            'fileName' => $doc->getFileName(),
            'fileUrl' => $doc->getFileUrl(),
            'fileType' => $doc->getFileType()
        ], $documents);

        return $this->json($data);
    }
}

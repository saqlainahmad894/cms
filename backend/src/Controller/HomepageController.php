<?php

namespace App\Controller\Admin;

use App\Entity\HomepageImage;
use App\Entity\HomepageText;
use App\Entity\TeamMember;
use App\Repository\HomepageImageRepository;
use App\Repository\HomepageTextRepository;
use App\Repository\TeamMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/homepage')]
#[IsGranted('ROLE_ADMIN')]
class HomepageController extends AbstractController
{
    #[Route('/images', name: 'get_homepage_images', methods: ['GET'])]
    public function getImages(HomepageImageRepository $repo): JsonResponse
    {
        $images = $repo->findAll();
        $data = array_map(fn($img) => [
            'id' => $img->getId(),
            'type' => $img->getType(),
            'path' => $img->getImagePath(),
        ], $images);

        return $this->json($data);
    }

    #[Route('/images/upload', name: 'upload_homepage_image', methods: ['POST'])]
    public function uploadImage(Request $request, EntityManagerInterface $em, HomepageImageRepository $repo): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('image');
        $type = $request->get('type'); // banner or carousel

        if (!$file || !$type || !in_array($type, ['banner', 'carousel'])) {
            return $this->json(['message' => 'Invalid input'], 400);
        }

        // If banner, replace existing one
        if ($type === 'banner') {
            $existingBanner = $repo->findOneBy(['type' => 'banner']);
            if ($existingBanner) {
                $em->remove($existingBanner);
                $em->flush();
            }
        }

        // Ensure directory exists
        $targetDirectory = 'uploads/main';
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($targetDirectory, $filename);

        // Build full URL
        $fullUrl = $request->getSchemeAndHttpHost() . '/uploads/main/' . $filename;

        $image = new HomepageImage();
        $image->setType($type)
            ->setImagePath($fullUrl)
            ->setUploadedAt(new \DateTime());

        $em->persist($image);
        $em->flush();

        return $this->json(['message' => 'Image uploaded']);
    }

    #[Route('/texts', name: 'get_homepage_texts', methods: ['GET'])]
    public function getTexts(HomepageTextRepository $repo): JsonResponse
    {
        $texts = $repo->findAll();
        $data = array_map(fn($txt) => [
            'id' => $txt->getId(),
            'section' => $txt->getSection(),
            'content' => $txt->getContent(),
            'fontWeight' => $txt->getFontWeight(),
            'fontCategory' => $txt->getFontCategory(),
        ], $texts);

        return $this->json($data);
    }

    #[Route('/texts/add', name: 'add_homepage_text', methods: ['POST'])]
    public function addText(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $text = new HomepageText();
        $text->setSection($data['section'])
            ->setContent($data['content'])
            ->setFontWeight($data['fontWeight'])
            ->setFontCategory($data['fontCategory'])
            ->setCreatedAt(new \DateTime());

        $em->persist($text);
        $em->flush();

        return $this->json(['message' => 'Text added']);
    }

    #[Route('/texts/update/{id}', name: 'update_homepage_text', methods: ['PUT'])]
    public function updateText(int $id, Request $request, HomepageTextRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $text = $repo->find($id);
        if (!$text) {
            return $this->json(['message' => 'Text not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $text->setSection($data['section'] ?? $text->getSection());
        $text->setContent($data['content'] ?? $text->getContent());
        $text->setFontWeight($data['fontWeight'] ?? $text->getFontWeight());
        $text->setFontCategory($data['fontCategory'] ?? $text->getFontCategory());

        $em->flush();
        return $this->json(['message' => 'Text updated']);
    }

    #[Route('/texts/delete/{id}', name: 'delete_homepage_text', methods: ['DELETE'])]
    public function deleteText(int $id, HomepageTextRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $text = $repo->find($id);
        if (!$text) {
            return $this->json(['message' => 'Text not found'], 404);
        }

        $em->remove($text);
        $em->flush();

        return $this->json(['message' => 'Text deleted']);
    }

    #[Route('/teams', name: 'get_team_members', methods: ['GET'])]
    public function getTeams(TeamMemberRepository $repo): JsonResponse
    {
        $teams = $repo->findAll();
        $data = array_map(fn($tm) => [
            'id' => $tm->getId(),
            'name' => $tm->getName(),
            'bio' => $tm->getBio(),
            'image' => $tm->getImagePath(),
            'twitter' => $tm->getTwitter(),
            'instagram' => $tm->getInstagram(),
            'linkedin' => $tm->getLinkedin(),
            'email' => $tm->getEmail(),
        ], $teams);

        return $this->json($data);
    }


    #[Route('/teams/add', name: 'add_team_member', methods: ['POST'])]
    public function addTeamMember(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $file = $request->files->get('image');
        $name = $request->get('name');
        $bio = $request->get('bio');
        $twitter = $request->get('twitter');
        $instagram = $request->get('instagram');
        $linkedin = $request->get('linkedin');
        $email = $request->get('email');

        if (!$file || !$name || !$bio) {
            return $this->json(['message' => 'Invalid input'], 400);
        }

        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move('uploads/team', $filename);

        // ✅ Construct full URL using request host and port
        $fullUrl = $request->getSchemeAndHttpHost() . '/uploads/team/' . $filename;

        $team = new TeamMember();
        $team->setName($name)
            ->setBio($bio)
            ->setImagePath($fullUrl) // ✅ full URL
            ->setTwitter($twitter)
            ->setInstagram($instagram)
            ->setLinkedin($linkedin)
            ->setEmail($email)
            ->setCreatedAt(new \DateTime());

        $em->persist($team);
        $em->flush();

        return $this->json(['message' => 'Team member added']);
    }

    #[Route('/teams/update/{id}', name: 'update_team_member', methods: ['POST'])]
    public function updateTeamMember(int $id, Request $request, TeamMemberRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $team = $repo->find($id);
        if (!$team) {
            return $this->json(['message' => 'Team member not found'], 404);
        }

        $team->setName($request->get('name') ?? $team->getName());
        $team->setEmail($request->get('email') ?? $team->getEmail());
        $team->setBio($request->get('bio') ?? $team->getBio());
        $team->setTwitter($request->get('twitter') ?? $team->getTwitter());
        $team->setInstagram($request->get('instagram') ?? $team->getInstagram());
        $team->setLinkedin($request->get('linkedin') ?? $team->getLinkedin());

        if ($request->request->get('removeImage') === '1') {
            $team->setImagePath(null);
        }

        /** @var UploadedFile $image */
        $image = $request->files->get('image');
        if ($image) {
            $filename = uniqid() . '.' . $image->guessExtension();
            $image->move('uploads/team', $filename);
            $fullUrl = $request->getSchemeAndHttpHost() . '/uploads/team/' . $filename;
            $team->setImagePath($fullUrl);
        }

        $em->flush();

        return $this->json(['message' => 'Team member updated']);
    }

    #[Route('/teams/delete/{id}', name: 'delete_team_member', methods: ['DELETE'])]
    public function deleteTeamMember(int $id, TeamMemberRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $team = $repo->find($id);
        if (!$team) {
            return $this->json(['message' => 'Team member not found'], 404);
        }

        $em->remove($team);
        $em->flush();

        return $this->json(['message' => 'Team member deleted']);
    }
}

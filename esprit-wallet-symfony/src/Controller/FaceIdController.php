<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FaceIdService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class FaceIdController extends AbstractController
{
    #[Route('/face-id/register', name: 'app_face_id_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!isset($data['descriptor']) || !is_array($data['descriptor'])) {
            return new JsonResponse(['error' => 'Invalid descriptor data'], Response::HTTP_BAD_REQUEST);
        }

        $user->setFaceIdDescriptor($data['descriptor']);
        $user->setIsFaceIdEnabled(true);
        $user->setIs2faEnabled(false); // Disable Email 2FA
        $entityManager->flush();

        return new JsonResponse(['success' => 'Face ID registered successfully']);
    }

    #[Route('/face-id/verify-flow', name: 'app_face_id_verify_flow')]
    public function verifyFlow(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isFaceIdEnabled() || !$user->getFaceIdDescriptor()) {
            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('security/face_id_verify.html.twig');
    }

    #[Route('/face-id/verify-check', name: 'app_face_id_verify_check', methods: ['POST'])]
    public function verifyCheck(Request $request, FaceIdService $faceIdService): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!isset($data['descriptor']) || !is_array($data['descriptor'])) {
            return new JsonResponse(['error' => 'Invalid descriptor data'], Response::HTTP_BAD_REQUEST);
        }

        if ($faceIdService->verifyFace($user, $data['descriptor'])) {
            // Clear the pending state in session
            $request->getSession()->set('_face_id_pending', false);
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'error' => 'Face does not match'], Response::HTTP_UNAUTHORIZED);
    }

    #[Route('/face-id/toggle', name: 'app_face_id_toggle', methods: ['POST'])]
    public function toggle(EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getFaceIdDescriptor()) {
            return new JsonResponse(['error' => 'You must first register your face'], Response::HTTP_BAD_REQUEST);
        }

        $user->setIsFaceIdEnabled(!$user->isFaceIdEnabled());
        
        // If enabling Face ID, disable Email 2FA
        if ($user->isFaceIdEnabled()) {
            $user->setIs2faEnabled(false);
        }

        $entityManager->flush();

        return new JsonResponse([
            'enabled' => $user->isFaceIdEnabled(),
            'message' => $user->isFaceIdEnabled() ? 'Face ID enabled' : 'Face ID disabled'
        ]);
    }
}

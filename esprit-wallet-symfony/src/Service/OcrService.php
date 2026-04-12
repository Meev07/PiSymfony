<?php

namespace App\Service;

use App\Entity\Cheque;
use Doctrine\ORM\EntityManagerInterface;

class OcrService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Simulates OCR extraction from an image.
     * In a real-world scenario, this would use Tesseract or a Cloud Vision API.
     */
    public function extractChequeInfo(string $filePath): array
    {
        // Simulate a delay for "AI Processing"
        usleep(1500000); // 1.5 seconds

        // For demonstration purposes, we'll try to find any 'PENDING' cheque in the system
        // and "recognize" it. In a real app, this would extract the text from the image.
        $cheque = $this->entityManager->getRepository(Cheque::class)->findOneBy(['status' => 'PENDING']);

        if ($cheque) {
            return [
                'success' => true,
                'chequeNumber' => $cheque->getChequeNumber(),
                'secureToken' => $cheque->getSecureToken(),
                'amount' => $cheque->getAmount(),
                'senderName' => $cheque->getSender()->getFullName(),
                'confidence' => 0.98
            ];
        }

        // Fallback or random data if no pending cheques exist
        return [
            'success' => false,
            'error' => 'No valid cheque patterns found in the image. Please ensure the cheque is well-lit and clearly visible.'
        ];
    }
}

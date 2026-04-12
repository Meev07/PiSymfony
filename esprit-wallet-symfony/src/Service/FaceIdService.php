<?php

namespace App\Service;

use App\Entity\User;

class FaceIdService
{
    /**
     * Calculates the Euclidean distance between two face descriptors (128-float arrays).
     * 
     * @param array $descriptor1
     * @param array $descriptor2
     * @return float
     */
    public function calculateDistance(array $descriptor1, array $descriptor2): float
    {
        if (count($descriptor1) !== count($descriptor2)) {
            throw new \InvalidArgumentException('Descriptors must have the same dimension.');
        }

        $sumOfSquares = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $sumOfSquares += pow($descriptor1[$i] - $descriptor2[$i], 2);
        }

        return sqrt($sumOfSquares);
    }

    /**
     * Verifies if a captured descriptor matches the user's stored descriptor.
     * 
     * @param User $user
     * @param array $capturedDescriptor
     * @param float $threshold Lower means stricter matching (default 0.6 is good for face-api.js)
     * @return bool
     */
    public function verifyFace(User $user, array $capturedDescriptor, float $threshold = 0.6): bool
    {
        $storedDescriptor = $user->getFaceIdDescriptor();

        if (!$storedDescriptor) {
            return false;
        }

        try {
            $distance = $this->calculateDistance($storedDescriptor, $capturedDescriptor);
            return $distance < $threshold;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }
}

<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteDemandeData
{
    #[Assert\NotNull(message: 'Identifiant de demande manquant.')]
    #[Assert\Positive(message: 'Identifiant de demande invalide.')]
    public ?int $id = null;
}

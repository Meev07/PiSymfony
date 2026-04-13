<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class DecisionDemandeData
{
    #[Assert\NotNull(message: 'Identifiant de demande manquant.')]
    #[Assert\Positive(message: 'Identifiant de demande invalide.')]
    public ?int $id_dossier = null;

    #[Assert\NotBlank(message: 'Statut de décision manquant.')]
    #[Assert\Choice(
        choices: ['Accept', 'Rejet'],
        message: 'Statut de décision invalide.'
    )]
    public ?string $statut = null;
}

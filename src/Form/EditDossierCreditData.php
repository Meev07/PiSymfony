<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO de validation pour la modification d'une demande de crédit.
 */
class EditDossierCreditData
{
    #[Assert\NotBlank(message: 'Le montant est obligatoire.')]
    #[Assert\Positive(message: 'Le montant doit être un nombre positif.')]
    #[Assert\Range(
        min: 100,
        max: 500000,
        notInRangeMessage: 'Le montant doit être entre {{ min }} et {{ max }} DT.'
    )]
    public ?float $montant_demande = null;

    #[Assert\NotBlank(message: 'La durée est obligatoire.')]
    #[Assert\Positive(message: 'La durée doit être un nombre positif.')]
    #[Assert\Range(
        min: 1,
        max: 360,
        notInRangeMessage: 'La durée doit être entre {{ min }} et {{ max }} mois.'
    )]
    public ?int $duree_mois = null;

    #[Assert\NotBlank(message: "Le taux d'intérêt est obligatoire.")]
    #[Assert\Range(
        min: 0,
        max: 50,
        notInRangeMessage: "Le taux doit être entre {{ min }} et {{ max }} %."
    )]
    public ?float $taux_interet = 13;
}

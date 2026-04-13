<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class CreditSearchData
{
    #[Assert\Positive(message: 'La durée doit être un nombre positif.')]
    #[Assert\Range(
        min: 1,
        max: 360,
        notInRangeMessage: 'La durée doit être entre {{ min }} et {{ max }} mois.'
    )]
    public ?int $search_duree = null;
}

<?php

declare(strict_types=1);

namespace App\Domain\User\Validator\Constraints\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

/**
 * @Annotation
 */
class Password extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length(['min' => 6, 'minMessage' => 'Min {{ limit }} characters password']),
        ];
    }
}

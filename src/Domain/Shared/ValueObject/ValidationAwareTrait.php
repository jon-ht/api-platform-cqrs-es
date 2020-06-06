<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validation;

trait ValidationAwareTrait
{
    /**
     * Validates a value against a constraint or a list of constraints.
     *
     * @see https://symfony.com/doc/current/components/validator.html
     *
     * @param mixed                                              $value       The value to validate
     * @param Constraint|Constraint[]                            $constraints The constraint(s) to validate against
     * @param string|GroupSequence|(string|GroupSequence)[]|null $groups      The validation groups to validate. If none is given, "Default" is assumed
     *
     * @throws ValidationException
     */
    protected static function validate($value, $constraints = null, $groups = null): void
    {
        $validator = Validation::createValidator();

        $violations = $validator->validate($value, $constraints, $groups);

        if (0 !== \count($violations)) {
            // Ensure exception will be handled by API Platform listener
            throw new ValidationException($violations);
        }
    }
}

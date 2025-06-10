<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UniqueTypeEventNameForUser extends Constraint
{
    public string $message = 'Le type "{{ value }}" existe déjà dans votre liste.';
    
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

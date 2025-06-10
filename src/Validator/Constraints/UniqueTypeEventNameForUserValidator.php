<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Repository\TypeEventRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\TypeEvent;

class UniqueTypeEventNameForUserValidator extends ConstraintValidator
{
    public function __construct(
        private TypeEventRepository $typeEventRepository,
        private Security $security,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof TypeEvent) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $existing = $this->typeEventRepository->findOneBy([
            'name' => $value->getName(),
            'user' => $user,
        ]);

        if ($existing) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->getName())
                ->atPath('name') 
                ->addViolation();
        }
    }
}
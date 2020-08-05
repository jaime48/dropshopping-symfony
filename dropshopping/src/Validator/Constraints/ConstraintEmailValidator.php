<?php


namespace App\Validator\Constraints;

use App\Entity\Customers;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Repository\CustomersRepository;

class ConstraintEmailValidator extends ConstraintValidator
{
    public $customersRepository;

    public function __construct(CustomersRepository $customersRepository)
    {
        $this->customersRepository = $customersRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintEmail) {
            throw new UnexpectedTypeException($constraint, ConstraintEmail::class);
        }

    // custom constraints should ignore null and empty values to allow
    // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        $product =  $this->customersRepository->findOneBy(['email' => $value]);
        if ($product) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
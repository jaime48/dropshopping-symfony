<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintEmail extends Constraint
{
    public $message = 'The Email {{ string }} is already taken.';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }

}
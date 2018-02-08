<?php
namespace BackendBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintAgentDistrict extends Constraint
{
    public $message = 'Error';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

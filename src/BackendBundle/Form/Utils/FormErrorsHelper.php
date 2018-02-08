<?php
namespace BackendBundle\Form\Utils;

use Symfony\Component\Form\FormInterface;

/**
 * Class FormErrorsHelper
 */
class FormErrorsHelper
{
    /**
     * @param FormInterface $form
     * @return array
     */
    public static function getErrorsAsArray($form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $key => $child) {
            if ($err = self::getErrorsAsArray($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
    }
}

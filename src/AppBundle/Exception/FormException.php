<?php

namespace AppBundle\Exception;

use BadMethodCallException;
use RuntimeException;
use Symfony\Component\Form\FormInterface;

class FormException extends RuntimeException {
    /**
     * @var FormInterface
     */
    protected $receivedForm;

    public static function invalidForm(FormInterface $receivedForm)
    {
        $formException = new self('Received an invalid form');
        $formException->setReceivedForm($receivedForm);

        return $formException;
    }

    /**
     * @return FormInterface
     */
    public function getReceivedForm(): FormInterface
    {
        return $this->receivedForm;
    }

    /**
     * @param FormInterface $receivedForm
     */
    public function setReceivedForm(FormInterface $receivedForm): void
    {
        if (null !== $this->receivedForm) {
            throw new BadMethodCallException('Received form is already set');
        }

        $this->receivedForm = $receivedForm;
    }
}

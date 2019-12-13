<?php

namespace AppBundle\Controller;


use AppBundle\Exception\FormException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ActionFormsControllerTrait
{
    protected function receiveForm(Request $request, string $type, $dataObject)
    {
        /** @var FormInterface $receivedForm */
        $receivedForm = $this->createForm($type, $dataObject);

        $receivedForm->handleRequest($request);

        if (!$receivedForm->isValid() || !$receivedForm->isSubmitted()) {
            throw FormException::invalidForm($receivedForm);
        }

        return $dataObject;
    }

    protected function createEmptyFormView(string $type, string $receiveRouteName)
    {
        return $this->createFormView($type, $receiveRouteName, null);
    }

    protected function createFormView(string $type, string $receiveRouteName, $data)
    {
        $options = [
            'action' => $receiveRouteName,
        ];

        /** @var FormInterface $form */
        $form = $this->createForm($type, $data, $options);

        return $form->createView();
    }
}
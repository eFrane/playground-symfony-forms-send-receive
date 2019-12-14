<?php

namespace AppBundle\Controller;


use AppBundle\Exception\FormException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

trait UsesForms
{
    protected function receiveForm(Request $request, string $type, $dataObject)
    {
        /** @var FormInterface $receivedForm */
        $receivedForm = $this->createForm($type, $dataObject);

        $receivedForm->handleRequest($request);

        if (!$receivedForm->isValid() || !$receivedForm->isSubmitted()) {
            throw FormException::invalidForm($receivedForm);
        }

        $dataObject = $receivedForm->getData();

        return $dataObject;
    }

    /**
     * @param string              $type Form type class name
     * @param string              $receiveRouteName Route name for generateUrl
     * @param array<string,mixed> $receiveRouteParameters Route parameters for generateUrl
     * @return FormView
     */
    protected function createEmptyFormView(string $type, string $receiveRouteName, array $receiveRouteParameters = [])
    {
        return $this->createFormView($type, null, $receiveRouteName, $receiveRouteParameters);
    }

    /**
     * @param string              $type Form type class name
     * @param mixed|null          $data Data to pre-populate the form view
     * @param string              $receiveRouteName Route name for generateUrl
     * @param array<string,mixed> $receiveRouteParameters Route parameters for generateUrl
     * @return FormView
     */
    protected function createFormView(string $type, $data, string $receiveRouteName, array $receiveRouteParameters = [])
    {
        $options = [
            'action' => $this->generateUrl($receiveRouteName, $receiveRouteParameters),
        ];

        /** @var FormInterface $form */
        $form = $this->createForm($type, $data, $options);

        return $form->createView();
    }
}

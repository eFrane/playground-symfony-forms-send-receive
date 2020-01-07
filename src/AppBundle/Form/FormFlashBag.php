<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace AppBundle\Form;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class FormFlashBag extends FlashBag
{
    public const SERVICE_NAME = 'form_flashbag';
    public const NAME = 'forms';
    public const STORAGE_KEY = '_form_flashes';

    public function __construct()
    {
        parent::__construct(self::STORAGE_KEY);

        $this->setName(self::NAME);
    }

    public function flashFormData(FormInterface $form, Request $request)
    {
        $this->clear();
        $this->add($form->getName(), serialize($request->request->all()));
    }

    public function reevaluateFlashedForm(FormInterface $form): FormInterface
    {
        $serializedData = $this->get($form->getName())[0];

        $data = unserialize($serializedData);

        $request = new Request();
        $request->request->replace($data);
        $request->setMethod('POST');

        $form->handleRequest($request);

        return $form;
    }
}

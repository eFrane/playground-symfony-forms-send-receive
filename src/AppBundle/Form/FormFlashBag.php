<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace AppBundle\Form;


use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Serializer\Serializer;

class FormFlashBag extends FlashBag
{
    public const SERVICE_NAME = 'forms_flashbag';
    public const NAME = 'forms';
    public const STORAGE_KEY = 'forms';

    public function __construct()
    {
        parent::__construct(self::STORAGE_KEY);

        $this->setName(self::NAME);
    }

    public function flashForm(FormInterface $form)
    {
        $this->set($form->getName(), serialize($form->getData()));
    }

    public function getFlashedForm(FormInterface $form): FormInterface
    {
        $data = unserialize($this->get($form->getName()));
        $form->setData($data);

        $form->handleRequest();

        return $form;
    }
}

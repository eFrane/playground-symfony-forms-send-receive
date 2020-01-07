<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace AppBundle\Form;


use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

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
}

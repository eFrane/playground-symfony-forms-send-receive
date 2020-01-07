<?php
/**
 * @copyright 2019
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace AppBundle\Form;


use InvalidArgumentException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormHandler
{
    /**
     * @var FormBuilderInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $formType;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;


    public function __construct(FormFactoryInterface $formFactory, SessionInterface $session, UrlGeneratorInterface $urlGenerator)
    {
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    public function setFormType(string $formType)
    {
        $this->formType = $formType;
    }

    /**
     * @param mixed|null          $data Data to pre-populate the form view
     * @param string              $receiveRouteName Route name for generateUrl
     * @param array<string,mixed> $receiveRouteParameters Route parameters for generateUrl
     * @return FormView
     */
    public function createView($data, string $receiveRouteName, array $receiveRouteParameters = [])
    {
        $options = [
            'action' => $this->urlGenerator->generate($receiveRouteName, $receiveRouteParameters),
        ];

        /** @var FormInterface $form */
        $form = $this->formFactory->create($this->formType, $data, $options);

        /** @var FormFlashBag $formFlashBag */
        $formFlashBag = $this->session->getBag(FormFlashBag::NAME);
        if ($formFlashBag->has($form->getName())) {
            $form = $formFlashBag->reevaluateFlashedForm($form);
        }

        return $form->createView();
    }

    /**
     * @param string              $receiveRouteName Route name for generateUrl
     * @param array<string,mixed> $receiveRouteParameters Route parameters for generateUrl
     * @return FormView
     */
    public function createEmptyView(string $receiveRouteName, array $receiveRouteParameters = [])
    {
        return $this->createView(null, $receiveRouteName, $receiveRouteParameters);
    }

    public function processRequest(Request $request, $data = null)
    {
        $form = $this->formFactory->create($this->formType, $data);

        return FormSubmitResponseBuilder::create($request, $form, $this->session, $this->urlGenerator);
    }
}

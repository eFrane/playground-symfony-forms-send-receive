<?php
/**
 * @copyright 2019
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace AppBundle\Form;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormSubmitResponseBuilder
{
    /**
     * @var Response
     */
    protected $errorResponse;

    /**
     * @var FormInterface
     */
    protected $form;
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var RedirectResponse
     */
    protected $successResponse;

    /**
     * @var callable
     */
    protected $storageHandler;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * FormSubmitResponseBuilder constructor.
     * @param Request               $request
     * @param FormInterface         $form
     * @param SessionInterface      $session
     * @param UrlGeneratorInterface $urlGenerator
     */
    protected function __construct(
        Request $request,
        FormInterface $form,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->form = $form;
        $this->request = $request;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;

        $referrer = $request->headers->get('referer');

        $this->errorResponse = new RedirectResponse($referrer);
        $this->successResponse = new RedirectResponse($referrer);
    }

    /**
     * @param Request               $request
     * @param FormInterface         $form
     * @param SessionInterface      $session
     * @param UrlGeneratorInterface $urlGenerator
     * @return FormSubmitResponseBuilder
     */
    public static function create(
        Request $request,
        FormInterface $form,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator
    ) {
        return new self($request, $form, $session, $urlGenerator);
    }

    /**
     * @param callable $storageHandler
     * @return $this
     */
    public function store(callable $storageHandler): self
    {
        // TODO: validate storage handler

        $this->storageHandler = $storageHandler;

        return $this;
    }

    /**
     * @param string $route
     * @param array<string,mixed>  $routeParameters
     * @return $this
     */
    public function fail(string $route, array $routeParameters = []): self
    {
        $redirectUrl = $this->urlGenerator->generate($route, $routeParameters);

        $this->errorResponse = new RedirectResponse($redirectUrl);

        return $this;
    }

    /**
     * @param string $route
     * @param array  $routeParameters
     * @return $this
     */
    public function success(string $route, array $routeParameters = [])
    {
        $redirectUrl = $this->urlGenerator->generate($route, $routeParameters);

        $this->successResponse = new RedirectResponse($redirectUrl);

        return $this;
    }

    /**
     * @return RedirectResponse|Response
     */
    public function response()
    {
        $this->form->handleRequest($this->request);

        if (!$this->form->isSubmitted()) {
            return $this->errorResponse;
        }

        if (!$this->form->isValid()) {
            /** @var FormFlashBag $formFlashBag */
            $formFlashBag = $this->session->getBag(FormFlashBag::NAME);
            $formFlashBag->flashFormData($this->form, $this->request);

            return $this->errorResponse;
        }

        call_user_func($this->storageHandler, $this->form->getData());

        return $this->successResponse;
    }
}

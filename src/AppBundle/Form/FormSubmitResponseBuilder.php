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

        $referer = $request->headers->get('referer');

        $this->errorResponse = new RedirectResponse($referer);
        $this->successResponse = new RedirectResponse($referer);
    }

    public static function create(
        Request $request,
        FormInterface $form,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator
    ) {
        return new self($request, $form, $session, $urlGenerator);
    }

    public function store(callable $storageHandler)
    {
        // TODO: validate storage handler

        $this->storageHandler = $storageHandler;

        return $this;
    }

    public function fail(string $route, array $routeParameters = [])
    {
        $redirectUrl = $this->urlGenerator->generate($route, $routeParameters);

        $this->errorResponse = new RedirectResponse($redirectUrl);

        return $this;
    }

    public function success(string $route, array $routeParameters = [])
    {
        $redirectUrl = $this->urlGenerator->generate($route, $routeParameters);

        $this->successResponse = new RedirectResponse($redirectUrl);

        return $this;
    }

    public function response()
    {
        $this->form->handleRequest($this->request);

        if (!$this->form->isSubmitted()) {
            return $this->errorResponse;
        }

        if (!$this->form->isValid()) {
            /** @var FormFlashBag $formFlashBag */
            $formFlashBag = $this->session->getBag(FormFlashBag::NAME);
            $formFlashBag->flashForm($this->form);

            return $this->errorResponse;
        }

        call_user_func($this->storageHandler, $this->form->getData());

        return $this->successResponse;
    }
}

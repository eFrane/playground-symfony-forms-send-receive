<?php

namespace AppBundle\Controller;

use AppBundle\Exception\FormException;
use AppBundle\Form\Type\TaskType;
use AppBundle\Model\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    use ActionFormsControllerTrait;

    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->render(
            'default/index.html.twig',
            [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]
        );
    }

    /**
     * @Route("/form", name="form.show", methods={"GET"})
     */
    public function showFormAction()
    {
        return $this->render(
            'form_view.html.twig',
            [
                'form' => $this->createEmptyFormView(TaskType::class, 'form.receive'),
            ]
        );
    }

    /**
     * @Route("/form", name="form.receive", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function receiveFormAction(Request $request)
    {
        try {
            $task = $this->receiveForm($request, TaskType::class, new Task());
        } catch (FormException $e) {
            // handle form exception
        }

        return $this->render('form_view.html.twig', compact('task'));
    }
}

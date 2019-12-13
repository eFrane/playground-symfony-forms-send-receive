<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\TaskType;
use AppBundle\Model\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
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
        $form = $this->createForm(TaskType::class, new Task(), ['action' => $this->generateUrl('form.receive')]);

        return $this->render(
            'form_view.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/form", name="form.receive", methods={"POST"})
     */
    public function receiveFormAction(Request $request)
    {
        $task = new Task();
        $receivedForm = $this->createForm(TaskType::class, $task);

        $receivedForm->handleRequest($request);

        if ($receivedForm->isValid() && $receivedForm->isSubmitted()) {
            $task = $receivedForm->getData();

            var_export($task); exit;
        }
    }
}

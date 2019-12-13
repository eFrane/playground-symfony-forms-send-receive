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
    use UsesForms;

    /**
     * super lazy way of having a "saved task" to edit
     *
     * @var Task
     */
    protected $savedTask;

    public function __construct()
    {
        $this->savedTask = new Task();

        $this->savedTask->setDueDate(new \DateTime('2019-12-12'));
        $this->savedTask->setTask('I am a task to be edited');
    }

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
     * @Route("/form", name="form.new", methods={"GET"})
     * @return Response
     */
    public function newFormAction()
    {
        return $this->render(
            'form_view.html.twig',
            [
                'form' => $this->createEmptyFormView(TaskType::class, 'form.receive.new'),
            ]
        );
    }

    /**
     * @Route("/form/edit", name="form.edit", methods={"GET"})
     * @return Response
     */
    public function editFormAction()
    {
        return $this->render(
            'form_view.html.twig',
            [
                'form' => $this->createFormView(TaskType::class, 'form.receive.edit', $this->savedTask),
            ]
        );
    }

    /**
     * @Route("/form", name="form.receive.new", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function receiveNewFormAction(Request $request)
    {
        try {
            $task = $this->receiveForm($request, TaskType::class, new Task());
        } catch (FormException $e) {
            // handle form exception
        }

        return $this->render('form_view.html.twig', compact('task'));
    }

    /**
     * @Route("/form/edit", name="form.receive.edit", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function receiveEditFormAction(Request $request)
    {
        try {
            // since we know this here as it's hard coded, we can just pass
            // $this->savedTask, but in theory the existing Model/DTO
            // could be fetched via Route / Get-Parameters

            $task = $this->receiveForm($request, TaskType::class, $this->savedTask);
        } catch (FormException $e) {
            // handle form exception
        }

        return $this->render('form_view.html.twig', compact('task'));
    }
}

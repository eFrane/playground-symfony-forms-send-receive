<?php

namespace AppBundle\Controller;

use AppBundle\Exception\FormException;
use AppBundle\Form\Type\TaskType;
use AppBundle\Model\Task;
use DateTime;
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

        $this->savedTask->setDueDate(new DateTime('2019-12-12'));
        $this->savedTask->setTask('I am a task to be edited');
    }

    /**
     * @Route("/", name="homepage")
     *
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
     *
     * @return Response
     */
    public function newFormAction()
    {
        return $this->render(
            'form_view.html.twig',
            [
                'form' => $this->createEmptyFormView(
                    TaskType::class,
                    'form.receive'
                ),
            ]
        );
    }

    /**
     * @Route("/form/edit", name="form.edit", methods={"GET"})
     *
     * @return Response
     */
    public function editFormAction()
    {
        return $this->render(
            'form_view.html.twig',
            [
                'form' => $this->createFormView(
                    TaskType::class,
                    $this->savedTask,
                    'form.receive',
                    ['taskId' => 1] // simulate an existing task
                ),
            ]
        );
    }

    /**
     * @Route("/form/{taskId}", name="form.receive", methods={"POST"})
     * @param Request  $request
     * @param int|null $taskId
     *
     * @return Response
     */
    public function receiveFormAction(Request $request, int $taskId = null)
    {
        $task = new Task();

        if (null !== $taskId) {
            // "load" task
            $task = $this->savedTask;
        }

        try {
            $task = $this->receiveForm($request, TaskType::class, $task);
        } catch (FormException $e) {
            // handle form exception
        }

        // In a real application this should be a redirect!
        return $this->render('form_view.html.twig', compact('task'));
    }
}

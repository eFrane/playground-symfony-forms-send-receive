<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\FormHandler;
use AppBundle\Form\Type\TaskType;
use AppBundle\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FormHandler
     */
    protected $formHandler;

    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * DefaultController constructor.
     * @param EntityManagerInterface $entityManager
     * @param FormHandler            $formHandler
     */
    public function __construct(EntityManagerInterface $entityManager, FormHandler $formHandler)
    {
        $this->entityManager = $entityManager;

        $this->formHandler = $formHandler;
        $this->formHandler->setFormType(TaskType::class);

        $this->taskRepository = $entityManager->getRepository('AppBundle:Task');
    }

    /**
     * @Route("/", name="task.list")
     *
     * @return Response
     */
    public function listAction()
    {
        $tasks = $this->taskRepository->findAll();

        // replace this example code with whatever you need
        return $this->render(
            'default/index.html.twig',
            compact('tasks')
        );
    }

    /**
     * @Route("/task", name="task.new", methods={"GET"})
     *
     * @return Response
     */
    public function newTaskAction()
    {
        return $this->render(
            'form_view.html.twig',
            ['form' => $this->formHandler->createEmptyView('task.save')]
        );
    }

    /**
     * @Route("/task/{taskId}", name="task.edit", methods={"GET"})
     *
     * @param int $taskId
     * @return Response
     */
    public function editTaskAction(int $taskId)
    {
        $task = $this->taskRepository->find($taskId);

        return $this->render(
            'form_view.html.twig',
            [
                'form' => $this->formHandler->createView(
                    $task,
                    'task.save',
                    ['taskId' => $task->getId()]
                ),
                'task' => $task,
            ]
        );
    }

    /**
     * @Route(
     *     "/task/{taskId}",
     *     name="task.save",
     *     methods={"POST"},
     *     requirements={"taskId": "\d+"}
     * )
     *
     * @param Request  $request
     * @param int|null $taskId
     * @return RedirectResponse
     */
    public function saveTaskAction(Request $request, int $taskId = null)
    {
        $storedTask = $this->taskRepository->findOrCreate($taskId);

        return $this->formHandler->processRequest($request, $storedTask)
            ->store(
                function (Task $taskFromForm) {
                    $this->entityManager->persist($taskFromForm);
                    $this->entityManager->flush();
                }
            )
            ->success('task.list')
            ->response();
    }

    /**
     * @Route("task/delete/{taskId}", name="task.delete", methods={"GET", "DELETE"})
     *
     * @param $taskId
     * @return RedirectResponse
     */
    public function deleteTaskAction(int $taskId)
    {
        $task = $this->taskRepository->find($taskId);

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('task.list');
    }
}

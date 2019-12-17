<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Exception\FormException;
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
    use UsesForms;

    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $entityManager->getRepository('AppBundle:Task');
    }

    /**
     * @Route("/", name="task.list")
     *
     * @return Response
     */
    public function indexAction()
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
            [
                'form' => $this->createEmptyFormView(
                    TaskType::class,
                    'task.save'
                ),
            ]
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
                'form' => $this->createFormView(
                    TaskType::class,
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
        if (null === $taskId) {
            $task = new Task();
        } else {
            $task = $this->taskRepository->find($taskId);
        }

        try {
            $task = $this->receiveForm($request, TaskType::class, $task);

            $this->entityManager->persist($task);
            $this->entityManager->flush();
        } catch (FormException $e) {
            // handle form exception
        }

        return $this->redirectToRoute('task.list');
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

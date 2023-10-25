<?php

namespace App\ETMF\EventListener;

use App\ETMF\Entity\Workflow;
use App\ETMF\Entity\WorkflowStep;
use App\ETMF\Entity\WorkflowWorkflowStep;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class WorkflowStepCreation
{
    public $kernel;
    public $fs;

    public function __construct(KernelInterface $kernel, Filesystem $fs)
    {
        $this->kernel = $kernel;
        $this->fs = $fs;
    }

    /**
     * After Created.
     */
    public function postPersist(LifecycleEventArgs $args): bool
    {
        return $this->update($args);
    }

    /**
     * After Updated.
     */
    public function postUpdate(LifecycleEventArgs $args): bool
    {
        return true;
    }

    private function update(LifecycleEventArgs $args): bool
    {
        $update = false;

        /** @var Workflow $entity */
        $entityWorkflow = $args->getObject();

        $class = get_class($entityWorkflow);
        switch ($class) {
            case "App\ESM\Entity\Workflow":
                $update = true;
                break;
        }

        if (!$update || null === $entityWorkflow->getStepCountValidation()) {
            return false;
        }

        $countStepValidation = $entityWorkflow->getStepCountValidation();

        for ($n=1; $n <= $countStepValidation; ++$n) {

            $step = $args->getObjectManager()->getRepository(WorkflowStep::class)->findOneBy(['position'=>$n]);
            if (null !== $step) {
                $entityWorkflowStep = new WorkflowWorkflowStep();
                $entityWorkflowStep->setWorkflow($entityWorkflow);
                $entityWorkflowStep->setWorkflowStep($step);
                // Persist and flush
                $args->getObjectManager()->persist($entityWorkflowStep);
            }
        }

        $args->getObjectManager()->persist($entityWorkflow);
        $args->getObjectManager()->flush();

        return true;
    }
}

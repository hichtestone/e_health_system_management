<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Training;
use App\ESM\Entity\User;
use App\ESM\Form\TrainingType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Request;

class TrainingHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return TrainingType::class;
    }

    /**
     * @param $data
     */
    protected function process($data): void
    {
        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($data)) {
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }

    /**
     * @param Training $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        if ('GET' === $request->getMethod()) {
            // check si id users
            if ($json = $request->get('user_ids')) {
                $json = json_decode($json, true);
                $json = array_map(function ($item) {
                    return $item['id'];
                }, $json);
            } else {
                $json = [];
            }

            $userRep = $this->entityManager->getRepository(User::class);
            foreach ($json as $userId) {
                $user = $userRep->find($userId);
                $data->addUser($user);
            }
        }

        return parent::handle($request, $data, $options);
    }
}

<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Contact;
use App\ESM\Entity\DropdownList\ContactTypeRecipient;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\User;
use App\ESM\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Request;

class ContactHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return ContactType::class;
    }

    /**
     * @param $data
     */
    protected function process($data): void
    {
        if (1 === $data->getTypeRecipient()->getId()) {
            $data->resetIntervenants();
        } else {
            $data->resetInterlocutors();
        }

        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($data)) {
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }

    /**
     * @param Contact $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $options['from'] = $options['from'] ?? null;
        if (null !== $options['from']) {
            if ('intervenant' === $options['from']) {
                // si on souhaite créer un contact depuis listgen  users (intervenants)
                $contactTypeRecipient = $this->entityManager->getRepository(ContactTypeRecipient::class)->find('2');
                $data->setTypeRecipient($contactTypeRecipient);
            } elseif ('interlocutor' === $options['from']) {
                // si on souhaite créer un contact depuis listgen interlocutors
                $contactTypeRecipient = $this->entityManager->getRepository(ContactTypeRecipient::class)->find('1');
                $data->setTypeRecipient($contactTypeRecipient);
            }
        }

        if ('GET' === $request->getMethod()) {
            if ($json = $request->get('user_ids')) {
                $json = json_decode($json, true);
                $json = array_map(function ($item) {
                    return $item['id'];
                }, $json);

                $userRep = $this->entityManager->getRepository(User::class);
                foreach ($json as $userId) {
                    $user = $userRep->find($userId);
                    $data->addIntervenant($user);
                }
            }

            if ($json = $request->get('interlocutors_ids')) {
                $json = json_decode($json, true);
                $json = array_map(function ($item) {
                    return $item['id'];
                }, $json);

                $interlocutorRep = $this->entityManager->getRepository(Interlocutor::class);
                foreach ($json as $interlocutorRepId) {
                    $interlocutor = $interlocutorRep->find($interlocutorRepId);
                    $data->addInterlocutor($interlocutor);
                }
            }
        }

        return parent::handle($request, $data, $options);
    }
}

<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Institution;
use App\ESM\Form\InstitutionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Cocur\Slugify\Slugify;

class InstitutionHandler extends AbstractFormHandler
{
    private $entityManager;

    public const typeIdNoFiness = [4, 7, 9, 10, 13, 14, 15, 17, 19];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return InstitutionType::class;
    }

    /**
     * @param Institution $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {

            $this->process($data);

            return true;
        }

        return false;
    }

    /**
     * @param Institution $data
     */
    protected function process($data): void
    {
		$slugify = new Slugify();

		if (1 !== $data->getCountry()->getId() || in_array($data->getInstitutionType()->getId(), self::typeIdNoFiness)) {
            $data->setFiness(str_repeat('0', 9));
            $data->setSiret(str_repeat('0', 14));
        }

		$data->setSlug($slugify->slugify($data->getName()));

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}

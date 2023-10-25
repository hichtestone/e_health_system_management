<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\Drug;
use App\ESM\FormHandler\DrugHandler;
use App\ESM\ListGen\Admin\DrugList;
use App\ESM\Repository\DrugRepository;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/drugs")
 */
class DrugController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="admin.drugs.index")
     * @Security("is_granted('DRUG_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $displayArchived = in_array('ROLE_DRUG_ARCHIVE', $this->getUser()->getRoles()) ? true : false;

        $list = $lgm->getListGen(DrugList::class);

        return $this->render('admin/drug/index.html.twig', [
            'list' => $list->getList($displayArchived, $translator),
        ]);
    }

    /**
     * @Route("/admin/ajax/drugs", name="admin.drugs.index.ajax")
     * @Security("is_granted('DRUG_LIST')")
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $displayArchived = in_array('ROLE_DRUG_ARCHIVE', $this->getUser()->getRoles()) ? true : false;

        // listgen handle request
        $list = $lgm->getListGen(DrugList::class);
        $list = $list->getList($displayArchived, $translator);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

    /**
     * @Route("/new", name="admin.drug.new")
     * @Security("is_granted('DRUG_CREATE') and is_granted ('DRUG_LIST')")
     */
    public function new(Request $request, DrugHandler $drugHandler): Response
    {
        $drug = new Drug();

        if ($drugHandler->handle($request, $drug)) {
            return $this->redirectToRoute('admin.drugs.index');
        }

        return $this->render('admin/drug/create.html.twig', [
            'form' => $drugHandler->createView(),
            'action' => 'create',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.drug.edit", requirements={"id"="\d+"})
     * @Security("is_granted('DRUG_EDIT', drug) and is_granted ('DRUG_LIST')")
     */
    public function edit(Request $request, Drug $drug, DrugHandler $drugHandler): Response
    {
        if ($drugHandler->handle($request, $drug)) {
            return $this->redirectToRoute('admin.drugs.index', [
                'id' => $drug->getId(),
            ]);
        }

        return $this->render('admin/drug/create.html.twig', [
            'form' => $drugHandler->createView(),
            'action' => 'edit',
        ]);
    }

	/**
	 * @Route("/{id}/show", name="admin.drug.show", requirements={"id"="\d+"})*
	 * @ParamConverter("drug", options={"id"="id"})
	 * @Security("is_granted('DRUG_LIST', drug)")
	 *
	 * @param Drug $drug
	 * @return Response
	 */
    public function show(Drug $drug): Response
    {
        // render
        return $this->render('admin/drug/show.html.twig', [
            'drug' => $drug,
        ]);
    }

    /**
     * @Route("/{id}/archive", name="admin.drug.archive", requirements={"id"="\d+"})
     * @Security("is_granted('DRUG_ARCHIVE', drug)")
     */
    public function archive(Request $request, Drug $drug): Response
    {
        $drug->setDeletedAt(new \DateTime());
        $this->entityManager->persist($drug);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.drugs.index', [
            'id' => $request->get('id'),
        ]);
    }

    /**
     * @Route("/{id}/restore", name="admin.drug.restore", requirements={"id"="\d+"})
     * @Security("is_granted('DRUG_RESTORE', drug)")
     */
    public function restore(Request $request, Drug $drug): Response
    {
        $drug->setDeletedAt(null);
        $this->entityManager->persist($drug);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.drugs.index', [
            'id' => $request->get('id'),
        ]);
    }

    /**
     * @Route("/api/trail-treatment", name="admin.drug.api.trait_treatment")
     *
     */
    public function trait_treatment(Request $request, DrugRepository $drugRepository): Response
    {
        $treatment = $request->query->all()['treatment'] ?? '';

        if ('' === $treatment) {
            return $this->json([]);
        }

        // Medicaments par trail treatment ayant des documents
        $drugs = $drugRepository->findByTreatmentHasDoc($treatment);

        $options = [];
        $options[] = '<option value="">-</option>'.PHP_EOL;
        foreach ($drugs as $drug) {
            $options[] = '<option value="'.$drug->getId().'">'.$drug->getName().'</option>'.PHP_EOL;
        }

        return $this->json([
             'html' => implode('', $options),
         ]);
    }
}

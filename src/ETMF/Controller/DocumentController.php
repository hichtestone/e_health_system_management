<?php

namespace App\ETMF\Controller;

use App\ESM\Entity\Center;
use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\Sponsor;
use App\ESM\Entity\Project;
use App\ETMF\Entity\Artefact;
use App\ETMF\Entity\Document;
use App\ETMF\Entity\DocumentVersion;
use App\ETMF\Entity\DropdownList\DocumentLevel;
use App\ETMF\Entity\Section;
use App\ETMF\Entity\Tag;
use App\ETMF\Entity\Zone;
use App\ETMF\Form\DocumentVersionType;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/etmf/document/version")
 */
class DocumentController extends AbstractController
{
    /**
     * @Route("/new", name="app.etmf.document.new")
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $documentVersion = new DocumentVersion();

        $form = $this->createForm(DocumentVersionType::class, $documentVersion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            try {
                $documents = $request->get('document_version');
                $entityManager = $this->getDoctrine()->getManager();
                $files = $request->files;

                foreach ($files as $uploadedFile) {

                    foreach ($uploadedFile['file'] as $file) {

                        $name = $file->getClientOriginalName();
                        $file->move($this->getParameter('DOCUMENTS_ETMF_PATH'), $name);

                        $document = new Document();
                        $document->setDescription($documents['description']);
                        $document->setName($name);

						$zone = '' !== $documents['zone'] ? $entityManager->getReference(Zone::class, (int)$documents['zone']) : null;
                        $document->setZone($zone);

						$section = '' !== $documents['section'] ? $entityManager->getReference(Section::class, (int)$documents['section']) : null;
						$document->setSection($section);

                        $artefact = '' !== $documents['artefact'] ? $entityManager->getReference(Artefact::class, (int)$documents['artefact']) : null;
						$document->setArtefact($artefact);

                        $sponsor = '' !== $documents['sponsor'] ? $entityManager->getReference(Sponsor::class, (int)$documents['sponsor']) : null;
						$document->setSponsor($sponsor);

						$project = '' !== $documents['project'] ? $entityManager->getReference(Project::class, (int)$documents['project']) : null;
                        $document->setProject($project);

                        $level = '' !== $documents['documentLevels'] ? $entityManager->getReference(DocumentLevel::class, (int)$documents['documentLevels']) : null;

                        if (null !== $level) {
                            $document->addDocumentLevel($level);
                        }

                        if(isset($documents['countries'])) {
                            foreach ($documents['countries'] as $country) {
                                $country = $entityManager->getReference(Country::class, (int) $country);
                                $document->addCountry($country);
                            }
                        }

                        if(isset($documents['centers'])) {
                            foreach ($documents['centers'] as $center) {
                                $center = $entityManager->getReference(Center::class, $center);
                                $document->addCenter($center);
                            }
                        }

                        if(isset($documents['tags'])) {
                            foreach ($documents['tags'] as $tag) {
                                $tag = $entityManager->getReference(Tag::class, $tag);
                                $document->addTag($tag);
                            }
                        }

                        $entityManager->persist($document);
                        $entityManager->flush();

                        $versions = $entityManager->getRepository(DocumentVersion::class)->findBy(['document' => $document]);

                        $version = new DocumentVersion();
                        $version->setDocument($document);
                        $version->setNumberVersion((count($versions) + 1));
                        $version->setFile($name);
                        $version->setCreatedAt(new \DateTime());
                        $version->setCreatedBy($this->getUser());

                        $version->setAuthor($data->getAuthor());
                        $version->setExpiredAt($data->getExpiredAt());
                        $version->setSignedAt($data->getSignedAt());
                        $version->setValidatedQaAt($data->getValidatedQaAt());
                        $version->setValidatedQaBy($data->getValidatedQaBy());

                        // date mise en application
                        if (null !== $data->getApplicationAt()) {
                            if (new DateTime() >= $data->getApplicationAt()) {
                                $version->setStatus(DocumentVersion::STATUS_PUBLISH);
                            } else {
                                $version->setStatus(DocumentVersion::STATUS_PENDING);
                            }

                            $version->setApplicationAt($data->getApplicationAt());
                        } else {
                            $version->setStatus(DocumentVersion::STATUS_PUBLISH);
                            $version->setApplicationAt(new DateTime());
                        }

                        if (null !== $data->setExpiredAt($data->getExpiredAt())) {
                            if (new DateTime() == $data->setExpiredAt()) {
                                $version->setStatus(DocumentVersion::STATUS_OBSOLETE);
                            }
                        }

                        if(isset($documents['tags'])) {
                            foreach ($documents['tags'] as $tag) {
                                $tag = $entityManager->getReference(Tag::class, $tag);
                                $version->addTag($tag);
                            }
                        }

                        $entityManager->persist($version);
                        $entityManager->flush();
                    }
                }

                $this->addFlash('success', 'Le document a est créé avec succès !');

            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Erreur lors de l\'enregistrement du document !');
                return $this->redirectToRoute('app.etmf.home', []);
            }

            return $this->redirectToRoute('app.etmf.home', []);
        }

        return $this->render('ETMF/admin/document/create.html.twig', [
            'form' => $form->createView(),
            'edit' => false,
        ]);
    }

	/**
	 * @Route("/sections/{zoneID}", name="document.get.section.xhr", methods="GET", requirements={"zoneID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("zone", options={"id"="zoneID"})
	 * @return Response
	 */
	public function getSections(Request $request, Zone $zone, SerializerInterface $serializer)
	{
		if (!$request->isXmlHttpRequest()) {
			throw new AccessDeniedException();
		}

		try {

			if (!$sections = $zone->getSections()) {
				return new JsonResponse(['sectionLabel' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The zone ' . $zone->getName() . ' does not have sections associated'], 500);
			}

			$status       = 200;
			$statusLabel  = 'OK';
			$errorMessage = '';
			$sections     = $serializer->serialize($sections, 'json', ['ignored_attributes' => ['zone', 'artefacts']]);

		} catch (Exception $exception) {

			$status       = 500;
			$statusLabel  = 'KO';
			$sections     = '';
			$errorMessage = $exception->getMessage();
		}

		return new JsonResponse(['sections' => $sections, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
	}

	/**
	 * @Route("/artefacts/{sectionID}", name="document.get.artefact.xhr", methods="GET", requirements={"sectionID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("section", options={"id"="sectionID"})
	 *
	 * @return Response
	 */
	public function getArtefacts(Request $request, Section $section, SerializerInterface $serializer)
	{
		if (!$request->isXmlHttpRequest()) {
			throw new AccessDeniedException();
		}

		try {
			if (!$artefacts = $section->getArtefacts()) {
				return new JsonResponse(['artefactLabel' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $section->getName() . ' does not have artefacts associated'], 500);
			}

			$status       = 200;
			$statusLabel  = 'OK';
			$errorMessage = '';
			$artefacts     = $serializer->serialize($artefacts, 'json', ['groups' => 'artefact']);

		} catch (Exception $exception) {

			$status       = 500;
			$statusLabel  = 'KO';
			$artefacts     = '';
			$errorMessage = $exception->getMessage();
		}

		return new JsonResponse(['artefacts' => $artefacts, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
	}

    /**
     * @Route("/levels/{artefactID}", name="document.get.level.xhr", methods="GET", requirements={"artefactID"="\d+"}, options={"expose"=true})
     * @ParamConverter("artefact", options={"id"="artefactID"})
     *
     * @return Response
     */
    public function getLevels(Request $request, Artefact $artefact, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$levels = $artefact->getArtefactLevels()) {
                return new JsonResponse(['levels' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $artefact->getName() . ' does not have levels associated'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $levels     = $serializer->serialize($levels, 'json', ['groups' => 'artefactLevel']);

        } catch (Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $levels     = '';
            $errorMessage = $exception->getMessage();
        }

        return new JsonResponse(['levels' => $levels, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }



    /**
     * @Route("/projects/{sponsorID}", name="document.get.project.xhr", methods="GET", requirements={"sponsorID"="\d+"}, options={"expose"=true})
     * @ParamConverter("sponsor", options={"id"="sponsorID"})
     *
     * @return Response
     */
    public function getProjects(Request $request, Sponsor $sponsor, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$projects = $sponsor->getProjects()) {
                return new JsonResponse(['projects' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $sponsor->getLabel() . ' does not have projects associated'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $projects     = $serializer->serialize($projects, 'json', ['groups' => 'project']);

        } catch (Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $projects     = '';
            $errorMessage = $exception->getMessage();
        }

        return new JsonResponse(['projects' => $projects, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }

    /**
     * @Route("/countries/{projectID}", name="document.get.country.xhr", methods="GET", requirements={"projectID"="\d+"}, options={"expose"=true})
     * @ParamConverter("project", options={"id"="projectID"})
     *
     * @return Response
     */
    public function getCountries(Request $request, Project $project, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$countries = $project->getCountries()) {
                return new JsonResponse(['projects' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $project->getName() . ' does not have countries associated'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $countries     = $serializer->serialize($countries, 'json', ['groups' => 'country']);

        } catch (Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $countries     = '';
            $errorMessage = $exception->getMessage();
        }

        return new JsonResponse(['countries' => $countries, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }

    /**
     * @Route("/centers/{projectID}", name="document.get.center.xhr", methods="GET", requirements={"projectID"="\d+"}, options={"expose"=true})
     * @ParamConverter("project", options={"id"="projectID"})
     *
     * @return Response
     */
    public function getCenters(Request $request, Project $project, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$centers = $project->getCenters()) {
                return new JsonResponse(['projects' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $project->getName() . ' does not have centers associated'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $centers     = $serializer->serialize($centers, 'json', ['groups' => 'center']);

        } catch (Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $centers     = '';
            $errorMessage = $exception->getMessage();
        }

        return new JsonResponse(['centers' => $centers, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }

    /**
     * @Route("/authors/{projectID}", name="document.get.author.xhr", methods="GET", requirements={"projectID"="\d+"}, options={"expose"=true})
     * @ParamConverter("project", options={"id"="projectID"})
     *
     * @return Response
     */
    public function getAuthors(Request $request, Project $project, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$authors = $project->getUserProjects()) {
                return new JsonResponse(['authors' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $project->getName() . ' does not have authors associated'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $authors     = $serializer->serialize($authors, 'json', ['groups' => 'userProject']);

        } catch (Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $authors     = '';
            $errorMessage = $exception->getMessage();
        }

        return new JsonResponse(['authors' => $authors, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }

    /**
     * @Route("/authorQa/{projectID}", name="document.get.authorQa.xhr", methods="GET", requirements={"projectID"="\d+"}, options={"expose"=true})
     * @ParamConverter("project", options={"id"="projectID"})
     *
     * @return Response
     */
    public function getAuthorQa(Request $request, Project $project, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$authorQa = $project->getUserProjects()) {
                return new JsonResponse(['authorQa' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $project->getName() . ' does not have authorQa associated'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';

            $data = $serializer->serialize($authorQa, 'json', ['groups' => 'userProject']);
            // afficher l'utilisateur connecté en premier s'il a accés le projet séléctionné
            $currentUser = [];
            $data = json_decode($data, true);

            foreach ($data as $key => $value) {
                if ($this->getUser()->getId() === $value['user']['id']) {
                    unset($data[$key]);
                    $currentUser[] = $value;
                }
            }

            $authorQa = json_encode(array_merge($currentUser, $data));


        } catch (Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $authorQa     = '';
            $errorMessage = $exception->getMessage();
        }

        return new JsonResponse(['authorQa' => $authorQa, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }


    /**
     * @Route("/sponsors/{projectID}", name="document.get.sponsor.xhr", methods="GET", requirements={"projectID"="\d+"}, options={"expose"=true})
     * @ParamConverter("project", options={"id"="projectID"})
     *
     * @return Response
     */
    public function getSponsor(Request $request, Project $project, SerializerInterface $serializer)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        try {
            if (!$sponsors = $project->getSponsor()) {
                return new JsonResponse(['sponsors' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $project->getName() . ' does not have sponsor associated'], 500);
            }

            $status       = 200;
            $statusLabel  = 'OK';
            $errorMessage = '';
            $sponsors     = $serializer->serialize($sponsors, 'json', ['groups' => 'sponsor']);

        } catch (Exception $exception) {

            $status       = 500;
            $statusLabel  = 'KO';
            $sponsors     = '';
            $errorMessage = $exception->getMessage();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $sponsors = !isset($sponsors) ? $entityManager->getRepository(Sponsor::class)->findAll() : $sponsors;

        return new JsonResponse(['sponsors' => $sponsors, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
    }


}

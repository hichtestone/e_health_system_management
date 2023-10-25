<?php

namespace App\ESM\Twig\Tree;

use App\ESM\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Class Treeview.
 */
class Treeview implements RuntimeExtensionInterface
{
    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var Environment
     */
    private $environnement;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Environment $environnement, EntityManagerInterface $entityManager)
    {
        $this->resolver = new OptionsResolver();
        $this->environnement = $environnement;
        $this->entityManager = $entityManager;
    }

    protected function setDefaultProp(): void
    {
        $this->settings = $this->resolver->resolve($this->settings);
    }

    public function configureOptions(): void
    {
        $this->resolver->setDefaults([
            'read_only' => true,
            'icon' => 'fa fa-folder-open',
            'show_all' => true,
        ]);
        $this->resolver->setRequired([]);
    }

	/**
	 * @param ChoiceView[][] $choices
	 * @param array $values
	 * @param array $options
	 * @param string|null $label if $choices are entities, define the label property
	 *
	 * @return string
	 *
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
    public function treeview(array $choices, array $values, array $options, ?string $label = null): string
	{
        $this->settings = $options;
        $this->configureOptions();
        $this->setDefaultProp();

        $choices = array_map(function ($choice) {
            if (ChoiceView::class === get_class($choice)) {
                return $choice->data;
            }

            return $choice;
        }, $choices);
        $choices = array_filter($choices, function ($choice) {
            return null === $choice->getParent();
        });

        return $this->environnement->render('twig/tree.html.twig', [
            'settings' => $this->settings,
            'choices' => $choices,
            'values' => $values,
            'options' => $options,
            'label' => $label,
        ]);
    }

    public function getJsTreeDataJson($choice, array $values): string
    {
        return json_encode([
            'CFvalue' => $choice->getId(), // on met la value pour la gestion des events js
            'selected' => in_array($choice->getId(), $values),
            'checkbox_disabled' => $this->settings['read_only'],
            'disabled' => $this->settings['read_only'],
            'opened' => $this->settings['show_all'],
            'icon' => $this->settings['icon'],
        ]);
    }

	/**
	 * @throws SyntaxError
	 * @throws RuntimeError
	 * @throws LoaderError
	 */
	public function tree(int $id, string $classParentName, string $classChildName, string $parentName, string $childName, string $labelName, string $selectfieldId, string $hiddenSelectfieldId, bool $isReadonly = false): string
    {
        $rolesExists = false;

        /** @var Profile $entity */
        $entity = null;

        // Get role list
        $roleProfileList = [];

        if ($id > 0) {
            $entity = $this->entityManager->getRepository($classParentName)->find($id);
        }

        if (null !== $entity) {
            $rolesExists = null !== $entity->getRoles() && 0 < $entity->getRoles()->count();
        }

        if ($rolesExists) {
            foreach ($entity->getRoles() as $role) {
                $roleProfileList[] = $role->getId();
            }
        }

        return $this->render($classChildName, $parentName, $childName, $labelName, $roleProfileList, $selectfieldId, $hiddenSelectfieldId, $isReadonly);
    }

	/**
	 * @throws RuntimeError
	 * @throws SyntaxError
	 * @throws LoaderError
	 */
	private function render(string $className, string $parentName, string $childName, string $labelName, array $roleProfileList, string $selectfieldId, string $hiddenSelectfieldId, bool $isReadonly): string
    {
        $rows = $this->getData($className, $parentName, $childName, $labelName);

        return $this->environnement->render('twig/treeview.html.twig',
            [
                'rows' => $rows,
                'roleProfileList' => $roleProfileList,
                'selectfieldId' => $selectfieldId,
                'hiddenSelectfieldId' => $hiddenSelectfieldId,
                'read_only' => $isReadonly,
            ]);
    }

    private function getData(string $className, string $parentName, string $childName, string $labelName): array
    {
        $reflectionExtractor = new ReflectionExtractor();
        $doctrineExtractor = new DoctrineExtractor($this->entityManager);
        $propertyInfo = new PropertyInfoExtractor(
        	// List extractors
            [$reflectionExtractor, $doctrineExtractor],
            // Type extractors
            [$doctrineExtractor, $reflectionExtractor]
        );
        $properties = $propertyInfo->getProperties($className);

        $entities = $this->entityManager->getRepository($className)->findBy([$parentName => null]);

        return $this->getRecursiveData($entities, $className, $properties, $parentName, $childName, $labelName);
    }

    /**
     * @param mixed $entities
     */
    private function getRecursiveData($entities, string $className, array $properties, string $parentName, string $childName, string $labelName): array
    {
        $data = [];
        $i = 0;
        foreach ($entities as $entity) {
            if (in_array($parentName, $properties)) {
                $method = $this->humanize('get'.$labelName);
                $parentMethod = 'get'.$this->humanize($parentName);
                $childMethod = 'get'.$this->humanize($childName);

                $labelMethod = in_array($labelName, $properties) ? $entity->$method() : '';
                $identifiant = $entity->getId() ?? 0;

                $data[$i]['name'] = $labelMethod;
                $data[$i]['id'] = $identifiant;

                // children
                if (null !== $entity->$childMethod() && 0 < $entity->$childMethod()->count()) {
                    $data[$i]['values'] = $this->getRecursiveData($entity->$childMethod(), $className, $properties, $parentName, $childName, $labelName);
                }

                ++$i;
            }
        }

        return $data;
    }

    /**
     * From vendor/symfony/form/FormRenderer.php  - line 283.
     */
    private function humanize(string $text): string
    {
        return ucfirst(strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $text))));
    }
}

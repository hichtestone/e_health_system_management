<?php

namespace App\ETMF\Twig\Tree;

use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

    public function __construct(Environment $environnement)
    {
        $this->resolver = new OptionsResolver();
        $this->environnement = $environnement;
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
     * @param string|null    $label   if $choices are entities, define the label property
     *
     * @return string
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function treeview(array $choices, array $values, array $options, ?string $label = null)
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
}

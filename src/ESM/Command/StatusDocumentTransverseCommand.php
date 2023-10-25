<?php

namespace App\ESM\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\ESM\Entity\DocumentTransverse;

/**
 * Class ValidationDocumentsCommand
 * @package App\Command
 */
class StatusDocumentTransverseCommand extends Command
{
    protected static $defaultName = 'app:enable_status:documents_transverses';

    private $em;

    protected function configure()
    {
        $this
            ->setDescription('Status des documents transverses')
        ;
    }

    /**
     * ValidationDocumentsCommand constructor.
     *
     * @param EntityManagerInterface $em EntityManagerInterface
     * @param ParameterBagInterface $parameters ParameterBagInterface
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameters)
    {
        parent::__construct();

        $this->em = $em;
    }

    /**
     * Execute
     *
     * @param InputInterface $input InputInterface
     * @param OutputInterface $output OutputInterface
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Log
        $io = new SymfonyStyle($input, $output);
        $io->title('Start documents validation');

        // Get all articles
        $documentsToValidate = $this->em->getRepository(DocumentTransverse::class)->findDocumentsToValidate();
        //dd($documentsToValidate);

        // Create progress bar
        $io->progressStart(count($documentsToValidate));

        foreach($documentsToValidate as $documentToValidate)
        {
            // Hydrate
            $documentToValidate->setIsValid(true);

            $this->em->persist($documentToValidate);
            $this->em->flush();

            $io->progressAdvance();
        }

        $io->success('status validé avec success');
        return 0;
    }
}
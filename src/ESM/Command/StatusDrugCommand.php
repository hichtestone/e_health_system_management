<?php

namespace App\ESM\Command;

use App\ESM\Entity\Drug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ValidationDocumentsCommand.
 */
class StatusDrugCommand extends Command
{
    protected static $defaultName = 'app:enable_status:drugs';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected function configure()
    {
        $this
            ->setDescription('Validation des médicaments')
        ;
    }

    /**
     * StatusDrugCommand constructor.
     *
     * @param EntityManagerInterface $em         EntityManagerInterface
     * @param ParameterBagInterface  $parameters ParameterBagInterface
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameters)
    {
        parent::__construct();

        $this->em = $em;
    }

    /**
     * Execute.
     *
     * @param InputInterface  $input  InputInterface
     * @param OutputInterface $output OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Log
        $io = new SymfonyStyle($input, $output);
        $io->title('Start drugs validation');

        $drugsToValidate = $this->em->getRepository(Drug::class)->DrugToValidate();

        $io->progressStart(count($drugsToValidate));

        foreach ($drugsToValidate as $drugToValidate)
        {
            // Hydrate
            $drugToValidate->setIsValid(false);

            $this->em->persist($drugToValidate);
            $this->em->flush();

            $io->progressAdvance();
        }

        // Success
        $io->success('validation success');

        return 0;
    }
}

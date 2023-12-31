<?php

return [
	Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class 				=> ['dev' => true, 'test' => true],
	Symfony\Bundle\DebugBundle\DebugBundle::class 							=> ['dev' => true, 'test' => true],
	Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle::class 					=> ['dev' => true, 'test' => true],
	Symfony\Bundle\FrameworkBundle\FrameworkBundle::class 					=> ['all' => true],
	Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class 				=> ['all' => true],
	Symfony\Bundle\TwigBundle\TwigBundle::class 							=> ['all' => true],
	Twig\Extra\TwigExtraBundle\TwigExtraBundle::class 						=> ['all' => true],
	Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class 	=> ['all' => true],
	Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class 					=> ['all' => true],
	Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class 		=> ['all' => true],
	Symfony\Bundle\SecurityBundle\SecurityBundle::class 					=> ['all' => true],
	Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class 			=> ['all' => true],
	Symfony\Bundle\MonologBundle\MonologBundle::class 						=> ['all' => true],
	Symfony\Bundle\MakerBundle\MakerBundle::class 							=> ['dev' => true],
	Knp\DoctrineBehaviors\DoctrineBehaviorsBundle::class 					=> ['all' => true],
	Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class 					=> ['all' => true],
	A2lix\AutoFormBundle\A2lixAutoFormBundle::class 						=> ['all' => true],
	A2lix\TranslationFormBundle\A2lixTranslationFormBundle::class 			=> ['all' => true],
	Nelmio\CorsBundle\NelmioCorsBundle::class 								=> ['all' => true],
	Vich\UploaderBundle\VichUploaderBundle::class 							=> ['all' => true],
	Liip\ImagineBundle\LiipImagineBundle::class 							=> ['all' => true],
	Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class 					=> ['all' => true],
	FOS\JsRoutingBundle\FOSJsRoutingBundle::class 							=> ['all' => true],
];
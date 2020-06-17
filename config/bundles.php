<?php

return [
    Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class => ['all' => true],
    Sensio\Bundle\DistributionBundle\SensioDistributionBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    Liip\FunctionalTestBundle\LiipFunctionalTestBundle::class => ['dev' => true, 'test' => true],
    Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle::class => ['dev' => true, 'test' => true],
];

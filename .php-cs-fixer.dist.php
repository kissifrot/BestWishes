<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
;

$config = new PhpCsFixer\Config();
return $config
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
    '@PSR12' => true,
    'no_unused_imports' => true,
    'no_superfluous_phpdoc_tags' => true,
    'array_syntax' => ['syntax' => 'short'],
    'native_function_invocation' => ['include' => ['@compiler_optimized']],
])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;

<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'no_unused_imports' => true,
    'no_superfluous_phpdoc_tags' => true,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setFinder($finder)
    ;
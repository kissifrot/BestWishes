<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/migrations',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    $rectorConfig->rule(\Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class);
    $rectorConfig->rule(\Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class);
    $rectorConfig->rule(\Rector\Php80\Rector\FuncCall\ClassOnObjectRector::class);
    $rectorConfig->rule(\Rector\Php81\Rector\Property\ReadOnlyPropertyRector::class);
    $rectorConfig->rule(\Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector::class);
    $rectorConfig->rule(\Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector::class);
    $rectorConfig->rule(\Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector::class);
    $rectorConfig->rule(\Rector\Php80\Rector\Class_\StringableForToStringRector::class);
    $rectorConfig->rule(\Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector::class);

    $rectorConfig->sets([
        \Rector\Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        \Rector\Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        \Rector\Symfony\Set\SensiolabsSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);
};

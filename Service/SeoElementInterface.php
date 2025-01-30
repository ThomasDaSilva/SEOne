<?php

namespace SEOne\Service;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('seone.type')]
interface SeoElementInterface
{
    public function supports(string $view): bool;
    public function getIdentifier(): string;
    public function getView(): string;
}

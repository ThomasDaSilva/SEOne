<?php

namespace SEOne\Service\SeoDefaultModels;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('seone.type')]
interface SeoElementInterface
{
    public function supports(string $view): bool;

    public function getIdentifier(): string;

    public function getView(): string;

    public function getPriority(): int;

    public function getSeoMicroData($id, string $type, array $params = []): string;

    public function getSeoPageTitle($id): string;

    public function getSeoPageH1($id, string $type): string;
}

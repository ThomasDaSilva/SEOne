<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function getSeoPageDesc($id): string;
}

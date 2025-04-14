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

namespace SEOne\Service;

use Psr\EventDispatcher\EventDispatcherInterface;
use SEOne\Service\SeoDefaultModels\SeoElementInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class SeoManager
{
    public function __construct(
        #[TaggedIterator('seone.type')]
        private readonly iterable $seoServices,
        protected EventDispatcherInterface $dispatcher,
    ) {
    }

    public function getSeoPageTitle($id = null, ?string $view = null): ?string
    {
        /** @var SeoElementInterface $seoService */
        $seoService = $this->getSeoServiceByView(view: $view);

        return $seoService->getSeoPageTitle(id: $id);
    }

    public function getSeoPageH1($id = null, ?string $view = null): ?string
    {
        /** @var SeoElementInterface $seoService */
        $seoService = $this->getSeoServiceByView(view: $view);

        return $seoService->getSeoPageH1(id: $id, type: $view);
    }

    public function getSeoPageDesc($id = null, ?string $view = null): ?string
    {
        /** @var SeoElementInterface $seoService */
        $seoService = $this->getSeoServiceByView(view: $view);

        return $seoService->getSeoPageDesc(id: $id);
    }

    public function getSeoMicroData($id = null, ?string $view = null, array $params = []): ?string
    {
        /** @var SeoElementInterface $seoService */
        $seoService = $this->getSeoServiceByView(view: $view);

        return $seoService->getSeoMicroData(id: $id, type: $view, params: $params);
    }

    public function getSeoServiceByView(string $view): ?SeoElementInterface
    {
        $seoServicesArray = iterator_to_array($this->seoServices);
        usort($seoServicesArray, static fn (SeoElementInterface $a, SeoElementInterface $b) => $b->getPriority() <=> $a->getPriority());
        foreach ($seoServicesArray as $seoService) {
            if ($seoService->supports($view)) {
                return $seoService;
            }
        }

        return null;
    }
}

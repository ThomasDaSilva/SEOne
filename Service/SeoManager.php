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
use SEOne\Event\SEOnePageTitleEvent;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\ConfigQuery;
use Thelia\Service\Model\LangService;

class SeoManager
{
    #[TaggedIterator('seone.type')]
    private iterable $seoServices;

    public function __construct(
        iterable                           $seoServices,
        private readonly RequestStack      $requestStack,
        private readonly LangService       $langService,
        protected EventDispatcherInterface $dispatcher
    )
    {
        $this->seoServices = $seoServices;
    }

    public function getPageTitle(string $view = null, ?int $identifier = null): ?string
    {
        $defaultTitle = ConfigQuery::read('store_name', '');

        $lang = $this->langService->getLang();

        if (!$view ??= $this->requestStack->getCurrentRequest()?->get('_view')) {
            return $defaultTitle;
        }

        $seoService = $this->getSeoServiceByView($view);
        $defaultTitle = $seoService->getPageTitle();

        $pageTitleEvent = new SEOnePageTitleEvent($defaultTitle, $view, $defaultId, $lang->getLocale());

        $this->dispatcher->dispatch(
            $pageTitleEvent,
            SEOnePageTitleEvent::BETTER_SEO_PAGE_TITLE);
    }

    public function getSeoData(string $type): ?SeoElementInterface
    {
        foreach ($this->seoServices as $service) {
            if ($service->supports($type)) {
                return $service;
            }
        }

        throw new \Exception("No SEO service found for type: $type");
    }

    protected function getSeoServiceByView(string $view): ?SeoElementInterface
    {
        foreach ($this->seoServices as $seoService) {
            if ($seoService->supports($view)) {
                return $seoService;
            }
        }
    }
}

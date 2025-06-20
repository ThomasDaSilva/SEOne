<?php

namespace SEOne\Hook;

use SEOne\Event\SEOneUrlEvent;
use SEOne\Event\SEOneUrlEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;

class MetaHook extends BaseHook
{
    public function __construct(
        protected RequestStack $requestStack,
        public ?EventDispatcherInterface $dispatcher = null,
        public ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
        $this->request = $requestStack->getCurrentRequest();
    }

    public function onMainHeadBottom(HookRenderEvent $event, EventDispatcherInterface $eventDispatcher): void
    {
        $view = $this->request->get('_view');
        if ($view && preg_match('#^[a-zA-Z0-9\-_\.]+$#', $view)) {
            $id = $this->request->get($view.'_id');

            $lang = $this->request->getSession()->getLang();

            $event->add(
                $this->render('meta_hook.html', [
                    'object_id' => $id,
                    'object_type' => $view,
                    'lang_id' => $lang->getId(),
                ])
            );
        }

        $canonicalUrlEvent = new SEOneUrlEvent();

        $eventDispatcher->dispatch(
            $event,
            SEOneUrlEvents::GENERATE_CANONICAL,
        );

        if ($canonicalUrlEvent->getUrl()) {
            $event->add('<link rel="canonical" href="'.$canonicalUrlEvent->getUrl().'">');
        }
    }
}

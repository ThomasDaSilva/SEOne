<?php

namespace SEOne\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class SeoFormHook extends BaseHook
{
    public function onTabSeoUpdateForm(HookRenderEvent $event): void
    {
        $objectId = $event->getArgument('id');
        $objectType = $event->getArgument('type');

        $event->add(
            $this->render(
                'seo-additional-fields.html',
                [
                    'object_id' => $objectId,
                    'object_type' => $objectType,
                ]
            )
        );
    }
}

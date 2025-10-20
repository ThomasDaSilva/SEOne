<?php

namespace SEOne\Hook;

use SEOne\Model\Robots;
use SEOne\Model\RobotsQuery;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ConfigQuery;
use Thelia\Model\LangQuery;
use Thelia\Tools\URL;

class ConfigurationHook extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $configRobotTxt = $this->getRobotTxtConfiguration();

        $event->add(
            $this->render('module_configuration.html', ['configRobotTxt' => $configRobotTxt])
        );
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                [
                    'type' => 'back',
                    'method' => 'onModuleConfiguration',
                ],
            ],
        ];
    }

    protected function getRobotTxtConfiguration(): array
    {
        $config = [];

        $robots = RobotsQuery::create()->find();

        if (0 >= $robots->count()) {
            if (!ConfigQuery::read('one_domain_foreach_lang')) {
                $robots[] = (new Robots())
                    ->setDomainName(URL::getInstance()->getBaseUrl())
                    ->setRobotsContent('')
                    ->save();
            } else {
                $langs = LangQuery::create()->filterByActive(true)->find();
                foreach ($langs as $lang) {
                    if ($url = $lang->getUrl()) {
                        $robots[] = (new Robots())
                            ->setDomainName($url)
                            ->setRobotsContent('')
                            ->save();
                    }
                }
            }
        }

        foreach ($robots as $robot) {
            $config[$robot->getId()][0] = $robot->getDomainName();
            $config[$robot->getId()][1] = $robot->getRobotsContent();
        }

        return $config;
    }
}

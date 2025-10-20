<?php

namespace SEOne\Loop;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use SEOne\Model\Robots;
use SEOne\Model\RobotsQuery;

use SEOne\Service\RobotTxtService;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * @method getId()
 * @method getDomainName()
 */
class RobotTxtLoop extends BaseLoop implements ArraySearchLoopInterface
{
    public function __construct(protected RobotTxtService $robotTxtService)
    {

    }

    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection();
    }

    public function buildArray(): array
    {
        $robots = [];
        $domains = $this->robotTxtService->getDomains();

        foreach ($domains as $domain) {
            $robot = $this->robotTxtService->getCurrentRobotTxt($domain);
            if (!$robot) {
                $robots = [
                    'id' => null,
                    'domain_name' => $domain,
                    'robots_content' => null
                ];
                continue;
            }

            $robots[] = [
                'id' => $robot->getId(),
                'domain_name' => $robot->getDomainName(),
                'robots_content' => $robot->getRobotsContent()
            ];
        }

        return $robots;
    }

    public function parseResults(LoopResult $loopResult): LoopResult
    {
        foreach ($loopResult->getResultDataCollection() as $data) {
            $loopResultRow = new LoopResultRow($data);

            $loopResultRow->set('ID', $data['id']);
            $loopResultRow->set('DOMAIN_NAME', $data['domain_name']);
            $loopResultRow->set('ROBOTS_CONTENT', $data['robots_content']);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}

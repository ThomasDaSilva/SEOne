<?php

namespace SEOne\Service\SeoDefaultModels;

trait SmartyCompatibilityTrait
{
    protected function explode($commaSeparatedValues): array
    {
        if (null === $commaSeparatedValues) {
            return [];
        }

        $array = explode(',', $commaSeparatedValues);

        if (array_walk(
            $array,
            function (&$item): void {
                $item = strtoupper(trim($item));
            }
        )) {
            return $array;
        }

        return [];
    }
}

<?php

namespace SEOne\Service;

use Thelia\Model\Product;
use Thelia\Model\ProductQuery;

class ProductSEO implements SeoElementInterface
{
    public function supports(string $view): bool
    {
        return $view === $this->getView();
    }

    public function getTheliaModel($objectId): Product
    {
        return ProductQuery::create()->filterById($objectId)->findOne();
    }

    public function getPageTitle(int $id, string $locale): string
    {
        $p = $this->getTheliaModel($id);
        $p->getTitle();
    }

    public function getIdentifier(): string
    {
        return 'product_id';
    }

    public function getView(): string
    {
        return 'product';
    }
}

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

namespace SEOne\Smarty\Plugins;

use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Util\PropelModelPager;
use SEOne\Event\SEOneMicroDataEvent;
use SEOne\Event\SEOneMicroDataEvents;
use SEOne\Event\SEOneStoreMicroDataEvent;
use SEOne\Event\SEOneStoreMicroDataEvents;
use SEOne\SEOne;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Exception\TaxEngineException;
use Thelia\Model\BrandI18nQuery;
use Thelia\Model\Category;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\Folder;
use Thelia\Model\FolderQuery;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Model\Product;
use Thelia\Model\ProductImageQuery;
use Thelia\Model\ProductPriceQuery;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\TaxEngine\TaxEngine;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class SEOneMicroDataPlugin extends AbstractSmartyPlugin
{
    public function __construct(
        protected RequestStack $requestStack,
        protected TaxEngine $taxEngine,
        protected EventDispatcherInterface $dispatcher
    ) {
    }

    public function getPluginDescriptors(): array
    {
        return [
            new SmartyPluginDescriptor('function', 'SEOneMicroData', $this, 'betterSeoMicroData'),
        ];
    }

    public function betterSeoMicroData($params): string
    {
        $objectId = null;
        $type = $params['type'] ?? $this->requestStack->getCurrentRequest()->get('_view');

        $lang = $this->requestStack->getCurrentRequest()->getSession()->getLang();

        if (!$lang) {
            $lang = LangQuery::create()->filterByByDefault(1)->findOne();
        }
        $microdata = null;

        switch ($type) {
            case 'product':
                $objectId = $params['id'] ?? $this->requestStack->getCurrentRequest()->get('product_id');
                $product = ProductQuery::create()->filterById($objectId)->findOne();
                $relatedProducts = null;

                if (\array_key_exists('related_products', $params)) {
                    $relatedProducts = \is_array($params['related_products']) ? $params['related_products'] : $this->explode($params['related_products']);
                }

                $microdata = $this->getProductMicroData($product, $lang, $relatedProducts);
                break;
            case 'category':
                $objectId = $params['id'] ?? $this->requestStack->getCurrentRequest()->get('category_id');
                if ($objectId) {
                    $page = $params['page'] ?? $this->requestStack->getCurrentRequest()->get('page') ?? 1;
                    $limit = $params['limit'] ?? $this->requestStack->getCurrentRequest()->get('limit') ??
                        SEOne::getConfigValue(SEOne::BETTER_SE0_LIMIT_CONFIG_KEY);

                    $category = CategoryQuery::create()->filterById($objectId)->findOne();
                    $microdata = $this->getCategoryMicroData($category, $lang, $page, $limit);
                }
                break;
            case 'folder':
                $objectId = $params['id'] ?? $this->requestStack->getCurrentRequest()->get('folder_id');
                if ($objectId) {
                    $folder = FolderQuery::create()->filterById($objectId)->findOne();
                    $microdata = $this->getFolderMicroData($folder, $lang);
                }
                break;
            case 'content':
                $objectId = $params['id'] ?? $this->requestStack->getCurrentRequest()->get('content_id');
                if ($objectId) {
                    $microdata = $this->getContentMicroData($objectId, $lang);
                }
                break;
        }

        $scriptsTag = '';

        $storeMicroData = $this->getStoreMicroData();

        if ($objectId) {
            $storeEvent = new SEOneStoreMicroDataEvent($storeMicroData, $type,
                $objectId, $lang->getLocale());

            $this->dispatcher->dispatch(
                $storeEvent,
                SEOneStoreMicroDataEvents::BETTER_SEO_STORE_MICRO_DATA);

            $storeMicroData = $storeEvent->getStoreMicrodata();

            $viewEvent = new SEOneMicroDataEvent($microdata, $type,
                $objectId, $lang->getLocale());

            $this->dispatcher->dispatch(
                $viewEvent,
                SEOneMicroDataEvents::BETTER_SEO_MICRO_DATA);
            $microdata = $viewEvent->getMicrodata();
        }

        $scriptsTag .= '<script type="application/ld+json">'.json_encode($storeMicroData, \JSON_UNESCAPED_UNICODE).'</script>';
        if (null !== $microdata) {
            $scriptsTag .= '<script type="application/ld+json">'.json_encode($microdata, \JSON_UNESCAPED_UNICODE).'</script>';
        }

        return $scriptsTag;
    }

    protected function getStoreMicroData(): array
    {
        $country = CountryQuery::create()->filterById(ConfigQuery::read('store_country', 64))->findOne();
        $microData = [
            '@context' => 'https://schema.org/',
            '@type' => 'Organization',
            'name' => ConfigQuery::read('store_name'),
            'description' => ConfigQuery::read('store_description'),
            'url' => ConfigQuery::read('url_site'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => ConfigQuery::read('store_address1').' '.ConfigQuery::read('store_address2').' '.ConfigQuery::read('store_address3'),
                'addressLocality' => ConfigQuery::read('store_city'),
                'addressCountry' => $country?->getIsoalpha2(),
                'postalCode' => ConfigQuery::read('store_zipcode'),
            ],
        ];

        return $microData;
    }

    protected function getProductMicroData(Product $product, Lang $lang, $relatedProducts = []): array
    {
        $product->setLocale($lang->getLocale());
        $image = ProductImageQuery::create()->filterByProductId($product->getId())->orderByPosition()->find()->getFirst();
        $pse = ProductSaleElementsQuery::create()->filterByProductId($product->getId())->filterByIsDefault(1)->findOne();
        $psePrice = ProductPriceQuery::create()->filterByProductSaleElementsId($pse->getId())->findOne();
        $taxCountry = $this->taxEngine->getDeliveryCountry();

        try {
            $taxedPrice = $product->getTaxedPrice(
                $taxCountry,
                $psePrice->getPrice()
            );
            if ($pse->getPromo()) {
                $taxedPrice = $product->getTaxedPromoPrice(
                    $taxCountry,
                    $psePrice->getPromoPrice()
                );
            }
        } catch (TaxEngineException $e) {
            $taxedPrice = null;
        }

        $imagePath = null;

        if ($image) {
            $baseSourceFilePath = ConfigQuery::read('images_library_path');
            if ($baseSourceFilePath === null) {
                $baseSourceFilePath = THELIA_LOCAL_DIR.'media'.DS.'images';
            } else {
                $baseSourceFilePath = THELIA_ROOT.$baseSourceFilePath;
            }
            $event = new ImageEvent();
            $sourceFilePath = $baseSourceFilePath.'/product/'.$image->getFile();

            $event->setSourceFilepath($sourceFilePath);
            $event->setCacheSubdirectory('product');

            try {
                $this->dispatcher->dispatch($event, TheliaEvents::IMAGE_PROCESS);
                $imagePath = $event->getFileUrl();
            } catch (\Exception $e) {
                $imagePath = $image->getFile();
            }
        }

        $microData = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->getTitle(),
            'image' => $imagePath,
            'description' => $product->getDescription(),
            'sku' => $product->getRef(),
            'offers' => [
                'url' => $product->getUrl(),
                'priceCurrency' => $this->requestStack->getCurrentRequest()->getSession()->getCurrency()->getCode(),
                'price' => $taxedPrice,
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $pse->getQuantity() > 0 ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock',
            ],
        ];

        if ($pse->getEanCode()) {
            $microData['gtin13'] = $pse->getEanCode();
        }

        $brandTitle = null;

        if ($brand = $product->getBrand()) {
            $brandTitle = BrandI18nQuery::create()
                ->filterById($brand->getId())
                ->findOne()
                ->getTitle();
        }

        if ($brandTitle) {
            $microData['brand']['@type'] = 'Brand';
            $microData['brand']['name'] = $brandTitle;
        }

        if ($weight = $pse->getWeight()) {
            $microData['shipping_weight'] = $weight.' kg';
        }

        if ($relatedProducts) {
            foreach ($relatedProducts as $relatedProductId) {
                $relatedProduct = ProductQuery::create()->filterById($relatedProductId)->findOne();
                $microData['isRelatedTo'][] = $this->getProductMicroData($relatedProduct, $lang);
            }
        }

        return $microData;
    }

    protected function getCategoryMicroData(Category $category, Lang $lang, $page, $limit): array
    {
        $category->setLocale($lang->getLocale());

        $products = $this->getProduct($category, $page, $limit);

        $itemListElement = [];

        $i = 1;
        foreach ($products as $product) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $i++,
                'url' => $product->getUrl(),
            ];
        }

        return [
            '@context' => 'https://schema.org/',
            '@type' => 'ItemList',
            'url' => $category->getUrl(),
            'numberOfItems' => \count($products),
            'itemListElement' => $itemListElement,
        ];
    }

    private function getProduct(Category $category, $page, $limit): PropelModelPager|ObjectCollection|array
    {
        if (null !== $limit) {
            return ProductQuery::create()->filterByCategory($category)->paginate($page, $limit);
        }

        return $category->getProducts();
    }

    protected function getFolderMicroData(Folder $folder, Lang $lang): array
    {
        $folder->setLocale($lang->getLocale());

        $microData = [
            '@context' => 'https://schema.org/',
            '@type' => 'Guide',
            'url' => $folder->getUrl(),
            'name' => $folder->getTitle(),
            'abstract' => $folder->getChapo(),
        ];

        return $microData;
    }

    protected function getContentMicroData($contentId, Lang $lang): ?array
    {
        $content = ContentQuery::create()->filterById($contentId)->findOne();

        if (null === $content) {
            return null;
        }

        $content->setLocale($lang->getLocale());

        $microData = [
            '@context' => 'https://schema.org/',
            '@type' => 'Article',
            'url' => $content->getUrl(),
            'name' => $content->getTitle(),
            'abstract' => $content->getChapo(),
        ];

        $defaultFoIdlder = $content->getDefaultFolderId();

        if (null !== $defaultFoIdlder) {
            $default_folder = FolderQuery::create()->findOneById($defaultFoIdlder);
            if (null !== $default_folder) {
                $default_folder->setLocale($lang->getLocale());
                $microData['isPartOf'] = [
                    'name' => $default_folder->getTitle(),
                    'url' => $default_folder->getUrl(),
                ];
            }
        }

        return $microData;
    }
}

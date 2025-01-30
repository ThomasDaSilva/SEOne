<?php

namespace SEOne;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;

class SEOne extends BaseModule
{
    public const DOMAIN_NAME = 'seone';
    public const BETTER_SE0_LIMIT_CONFIG_KEY = 'seone_limit';
    public const SEO_CANONICAL_META_KEY = 'seo_canonical_meta';

    public function postActivation(ConnectionInterface $con = null): void
    {
        if (!self::getConfigValue('is_initialized')) {
            $database = new Database($con);
            $database->insertSql(null, [__DIR__.'/Config/TheliaMain.sql']);
            self::setConfigValue('is_initialized', 1);
        }
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR.ucfirst(self::getModuleCode()).'/I18n/*'])
            ->autowire()
            ->autoconfigure();
    }
}

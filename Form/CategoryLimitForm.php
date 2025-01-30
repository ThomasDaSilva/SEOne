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

namespace SEOne\Form;

use SEOne\SEOne;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class CategoryLimitForm extends BaseForm
{
    public static function getName()
    {
        return 'seone_category_form_config';
    }

    protected function buildForm(): void
    {
        $this->formBuilder
            ->add(
                'category_limit',
                TextType::class, [
                    'required' => false,
                    'label' => Translator::getInstance()?->trans('category limit', [], SEOne::DOMAIN_NAME),
                    'label_attr' => [
                        'for' => 'category_limit',
                        'help' => Translator::getInstance()?->trans('limit products load with SEOone in your category page, use it for better performance', [], SEOne::DOMAIN_NAME),
                    ],
                    'data' => SEOne::getConfigValue(SEOne::BETTER_SE0_LIMIT_CONFIG_KEY),
                ]
            )
        ;
    }
}

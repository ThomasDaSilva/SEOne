<?php

namespace SEOne\Form;

use SEOne\SEOne;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class StoreSeoForm extends BaseForm
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    public static function getName(): string
    {
        return 'seone_store_form_config';
    }

    protected function buildForm(): void
    {
        $locale = $this->requestStack->getCurrentRequest()->getSession()->getAdminEditionLang()->getLocale();

        $this->formBuilder
            ->add(
                'title',
                TextType::class,
                [
                    'required' => false,
                    'label' => Translator::getInstance()?->trans('Store name', [], SEOne::DOMAIN_NAME),
                    'data' => SEOne::getConfigValue('title', null, $locale),
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'required' => false,
                    'label' => Translator::getInstance()?->trans('Store description', [], SEOne::DOMAIN_NAME),
                    'data' => SEOne::getConfigValue('description', null, $locale),
                ]
            )
            ->add(
                'keywords',
                TextType::class,
                [
                    'required' => false,
                    'label' => Translator::getInstance()?->trans('Keywords', [], SEOne::DOMAIN_NAME),
                    'data' => SEOne::getConfigValue('keywords', null, $locale),
                ]
            )
        ;
    }
}

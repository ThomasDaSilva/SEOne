<?php

namespace SEOne\Event;

use Thelia\Core\Event\ActionEvent;

class SEOnePageTitleEvent extends ActionEvent
{
    protected string $title;
    protected string $view;
    protected ?int $view_id;
    protected string $locale;

    public const BETTER_SEO_PAGE_TITLE = 'better.seo.page.title';

    public function __construct(string $title, string $view, ?int $view_id, string $locale)
    {
        $this->title = $title;
        $this->view = $view;
        $this->view_id = $view_id;
        $this->locale = $locale;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView($view): void
    {
        $this->view = $view;
    }

    public function getViewId(): ?int
    {
        return $this->view_id;
    }

    public function setViewId($view_id): void
    {
        $this->view_id = $view_id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }
}

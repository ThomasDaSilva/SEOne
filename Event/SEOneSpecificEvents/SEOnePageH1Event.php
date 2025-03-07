<?php

namespace SEOne\Event\SEOneSpecificEvents;

use Thelia\Core\Event\ActionEvent;

class SEOnePageH1Event extends ActionEvent
{
    protected string $title = "";
    protected string $view;
    protected ?int $view_id;

    public const BETTER_SEO_PAGE_H1 = 'better.seo.page.h1';

    public function __construct(string $view, ?int $view_id)
    {
        $this->view = $view;
        $this->view_id = $view_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): SEOnePageH1Event
    {
        $this->title = $title;
        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): SEOnePageH1Event
    {
        $this->view = $view;
        return $this;
    }

    public function getViewId(): ?int
    {
        return $this->view_id;
    }

    public function setViewId(?int $view_id): SEOnePageH1Event
    {
        $this->view_id = $view_id;
        return $this;
    }
}

<?php

namespace SEOne\Event\SEOneSpecificEvents;

use Thelia\Core\Event\ActionEvent;

class SEOneMicroDataEvent extends ActionEvent
{
    protected string $title = "";
    protected string $view;
    protected ?int $view_id;
    protected $parameters = [];
    public const BETTER_SEO_MICRO_DATA = 'better.seo.page.micro.data';

    public function __construct(string $view, ?int $view_id,array $parameters)
    {
        $this->view = $view;
        $this->view_id = $view_id;
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): SEOneMicroDataEvent
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): SEOneMicroDataEvent
    {
        $this->title = $title;
        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): SEOneMicroDataEvent
    {
        $this->view = $view;
        return $this;
    }

    public function getViewId(): ?int
    {
        return $this->view_id;
    }

    public function setViewId(?int $view_id): SEOneMicroDataEvent
    {
        $this->view_id = $view_id;
        return $this;
    }
}

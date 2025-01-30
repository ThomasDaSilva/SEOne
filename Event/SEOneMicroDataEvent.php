<?php

namespace SEOne\Event;

use Thelia\Core\Event\ActionEvent;

class SEOneMicroDataEvent extends ActionEvent
{
    protected array $microdata;
    protected string $view;
    protected int $view_id;
    protected string $locale;

    public function __construct(array $microdata, string $view, int $view_id, string $locale)
    {
        $this->microdata = $microdata;
        $this->view = $view;
        $this->view_id = $view_id;
        $this->locale = $locale;
    }

    public function getMicrodata(): array
    {
        return $this->microdata;
    }

    public function setMicrodata(array $microdata): void
    {
        $this->microdata = $microdata;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView($view): void
    {
        $this->view = $view;
    }

    public function getViewId(): int
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

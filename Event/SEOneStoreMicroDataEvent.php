<?php

namespace SEOne\Event;

use Thelia\Core\Event\ActionEvent;

class SEOneStoreMicroDataEvent extends ActionEvent
{
    protected array $storeMicrodata;
    protected string $view;
    protected int $view_id;
    protected string $locale;

    public function __construct(array $storeMicrodata, string $view, int $view_id, string $locale)
    {
        $this->storeMicrodata = $storeMicrodata;
        $this->view = $view;
        $this->view_id = $view_id;
        $this->locale = $locale;
    }

    public function getStoreMicrodata(): array
    {
        return $this->storeMicrodata;
    }

    public function setStoreMicrodata(array $storeMicrodata): void
    {
        $this->storeMicrodata = $storeMicrodata;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function getViewId(): int
    {
        return $this->view_id;
    }

    public function setViewId(int $view_id): void
    {
        $this->view_id = $view_id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}

<?php

namespace SEOne\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SEOneUrlEvent extends Event
{
    protected ?string $url;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl($url): static
    {
        if ($url !== null && $url[0] !== '/' && filter_var($url, \FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('The value "'.$url.'" is not a valid Url or Uri.');
        }

        $this->url = $url;

        return $this;
    }
}

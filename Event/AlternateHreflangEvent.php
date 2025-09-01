<?php

namespace SEOne\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;
use Thelia\Model\Lang;

class AlternateHreflangEvent extends Event
{
    const BASE_EVENT_NAME = 'get.alternate.hreflang';

    const DOMAIN_NAME = 'alternatehreflang';

    const CONFIG_KEY_HREFLANG_FORMAT = "hreflangFormat";

    protected Lang $lang;

    protected Request $request;

    /** @var string */
    protected string $url;

    public function __construct(Lang $lang, Request $request)
    {
        $this->lang = $lang;
        $this->request = $request;
    }

    public function getLang() : Lang
    {
        return $this->lang;
    }

    public function getRequest() : Request
    {
        return $this->request;
    }


    public function getUrl() : string
    {
        return $this->url;
    }


    public function setUrl(string $url) : self
    {
        $this->url = $url;
        return $this;
    }
}

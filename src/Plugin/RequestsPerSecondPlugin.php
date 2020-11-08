<?php

declare(strict_types = 1);

namespace Paba\Plugin;

class RequestsPerSecondPlugin extends AbstractComplexPlugin
{
    //Requests per second:    430.10 [#/sec] (mean)
    protected const TITLE = 'Requests per second';

    protected const REGEX = '@([0-9\.]+) \[\#\/([a-zA-Z]+)\](.*)@';
}

<?php

declare(strict_types = 1);

namespace Paba\Plugin;

class TimePerRequestPlugin extends AbstractComplexPlugin
{
    //Time per request:       2325.064 [ms] (mean)
    protected const TITLE = 'Time per request';

    protected const REGEX = '@([0-9\.]+) \[([a-zA-Z]+)\](.*)@';
}

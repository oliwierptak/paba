<?php

declare(strict_types = 1);

namespace Paba\Plugin;

class TransferRatePlugin extends AbstractComplexPlugin
{
    //Transfer rate:          104754.76 [Kbytes/sec] received
    protected const TITLE = 'Transfer rate';

    protected const REGEX = '@([0-9\.]+) \[([a-zA-Z\/]+)\](.*)@';

}

<?php

namespace Herrera\Annotations\Test;

use Herrera\Annotations\Convert\AbstractConvert;
use Herrera\Annotations\Tokens;

class TestConvert extends AbstractConvert
{
    public $tokens;

    public function __construct()
    {
        $this->result = 100;
    }

    protected function handle()
    {
        $this->result++;
    }

    protected function reset(Tokens $tokens)
    {
        $this->result = 0;
        $this->tokens = $tokens;
    }
}

<?php
declare(strict_types=1);


namespace PurrmannWebsolutions\LinkHandlerBundle\Tests\Fixtures;

class FixtureClass
{


    public function getInt(): int
    {
        return 1;
    }

    public function getArray(): array
    {
        return [];
    }

    public function getObject(): \stdClass
    {
        return new \stdClass();
    }

    public function getString(): string
    {
        return 'string';
    }
}

<?php

namespace Twomedia\PoliciesBuilder\Tests\DTOs;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\DTOs\IconCopyright;

class IconCopyrightTest extends TestCase
{
    /** @test */
    public function create_icon_copyright_object_from_named_constructor_and_allow_to_cast_to_string()
    {
        $copyright = IconCopyright::make("example.com");

        $this->assertEquals("Icons © example.com", $copyright->__toString());
    }
}

<?php

namespace Twomedia\PoliciesBuilder\Tests\DTOs;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\DTOs\Copyright;

class CopyrightTest extends TestCase
{
    /** @test */
    public function generate_copyright_object_from_author_source_and_description()
    {
        $copyright = Copyright::make('Picasso', 'Unsplash', 'Hero Image');

        $this->assertEquals('© Picasso / Unsplash', $copyright->toString());
    }
}

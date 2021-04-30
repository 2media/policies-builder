<?php

namespace Twomedia\PoliciesBuilder\Tests\Policies;

use PHPUnit\Framework\TestCase;
use Stringable;
use Twomedia\PoliciesBuilder\DTOs\Copyright;
use Twomedia\PoliciesBuilder\DTOs\IconCopyright;
use Twomedia\PoliciesBuilder\Policies\Imprint;

class ImprintTest extends TestCase
{
    /** @test */
    public function casts_image_copyrights_to_strings()
    {
        $imprint = Imprint::make()
            ->imageCopyrights([
                Copyright::make('Picasso', 'example.com'),
                IconCopyright::make('thenounproject.com'),
                new class() implements Stringable {
                    public function __toString()
                    {
                        return 'Anonymous Copyright Class';
                    }
                },
            ]);

        $this->assertEquals([
            'imageCopyrights' => [
                '© Picasso / example.com',
                'Icons © thenounproject.com',
                'Anonymous Copyright Class',
            ],
        ], $imprint->placeholders());
    }
}

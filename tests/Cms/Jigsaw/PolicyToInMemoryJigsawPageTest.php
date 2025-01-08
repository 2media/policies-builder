<?php

namespace Twomedia\PoliciesBuilder\Tests\Cms\Jigsaw;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Cms\Jigsaw\PolicyToInMemoryJigsawPage;
use Twomedia\PoliciesBuilder\Policies\Imprint;

class PolicyToInMemoryJigsawPageTest extends TestCase
{
    /** @test */
    public function it_returns_in_memory_jigsaw_page_for_given_policy_object_and_api_response()
    {
        $imprint = Imprint::make();
        $language = 'de';
        $metaTitle = 'Imprint';
        $html = '<h1>Hello World</h1>';

        $result = (new PolicyToInMemoryJigsawPage)->generate($imprint, $html, $language, $metaTitle);

        $this->assertIsArray($result);

        $this->assertEquals('_layouts.policy', $result['extends']);
        $this->assertEquals('de', $result['lang']);
        $this->assertEquals('de', $result['locale']);
        $this->assertEquals('Imprint', $result['meta_title']);
        $this->assertEquals('', $result['meta_description']);
        $this->assertEquals($html, $result['content']);
        $this->assertEquals('index-de-imprint', $result['filename']);
        $this->assertEquals('imprint', $result['policy_type']);
        $this->assertEquals('{locale}/{policy_type}', $result['path']);
    }
}

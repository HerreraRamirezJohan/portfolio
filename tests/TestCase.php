<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The suite asserts on the shell and the API, not on the asset
        // pipeline -- don't make it depend on `npm run build` having run.
        $this->withoutVite();
    }

    /**
     * Assert that each string appears somewhere in a JSON response's data.
     * (The raw body escapes non-ASCII as \uXXXX, so assertSee can't be used.)
     */
    protected function assertJsonMentions(TestResponse $response, string ...$texts): void
    {
        $body = json_encode($response->json(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach ($texts as $text) {
            $this->assertStringContainsString($text, $body);
        }
    }

    protected function assertJsonDoesNotMention(TestResponse $response, string ...$texts): void
    {
        $body = json_encode($response->json(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach ($texts as $text) {
            $this->assertStringNotContainsString($text, $body);
        }
    }
}

<?php

namespace Tests\Unit;

use App\Support\TechIcon;
use PHPUnit\Framework\TestCase;

class TechIconTest extends TestCase
{
    public function test_it_matches_skill_names_regardless_of_case_or_padding(): void
    {
        $this->assertSame('php', TechIcon::slug('PHP'));
        $this->assertSame('php', TechIcon::slug('php'));
        $this->assertSame('php', TechIcon::slug('  PHP  '));
    }

    public function test_it_returns_null_for_names_with_no_sensible_mark(): void
    {
        // These should render as plain text chips, not get an invented logo.
        $this->assertNull(TechIcon::slug('REST'));
        $this->assertNull(TechIcon::slug('5 Whys'));
        $this->assertNull(TechIcon::slug('Scrum'));
        $this->assertNull(TechIcon::slug(null));
        $this->assertNull(TechIcon::slug(''));
    }

    public function test_related_tools_share_a_mark(): void
    {
        $this->assertSame('python', TechIcon::slug('Pandas'));
        $this->assertSame('python', TechIcon::slug('NumPy'));
        $this->assertSame('docker', TechIcon::slug('Docker Compose'));
    }

    public function test_it_flags_the_sap_family_for_tinting(): void
    {
        $this->assertTrue(TechIcon::isSap('ABAP'));
        $this->assertTrue(TechIcon::isSap('OO ABAP'));
        $this->assertTrue(TechIcon::isSap('SAPUI5'));
        $this->assertTrue(TechIcon::isSap('CDS Views'));

        $this->assertFalse(TechIcon::isSap('Laravel'));
        $this->assertFalse(TechIcon::isSap('PostgreSQL'));
        $this->assertFalse(TechIcon::isSap(null));
    }
}

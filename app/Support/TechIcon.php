<?php

namespace App\Support;

/**
 * Maps a skill or stack name to an icon slug.
 *
 * Skill names are already proper nouns in the database ("PHP", "Docker
 * Compose"), so the icon is derived from the name rather than stored on the
 * row -- nothing to fill in through the admin, and the seeded data works as
 * it is. A name with no mark simply renders as a text chip, which is the
 * intended fallback: most entries (REST, 5 Whys, FSD / BRD Review) have no
 * sensible logo and should not get one.
 */
class TechIcon
{
    /**
     * Exact, lowercased name => icon slug.
     *
     * @var array<string, string>
     */
    private const MAP = [
        'php' => 'php',
        'laravel' => 'laravel',
        'python' => 'python',
        'pandas' => 'python',
        'numpy' => 'python',
        'java' => 'java',
        'spring boot' => 'java',
        'react' => 'react',
        'ionic' => 'react',
        'javascript' => 'javascript',
        'html/css' => 'html',
        'docker' => 'docker',
        'docker compose' => 'docker',
        'linux' => 'linux',
        'nginx' => 'nginx',
        'git' => 'git',
        'github' => 'github',
        'azure' => 'azure',
        'postgresql' => 'postgresql',
        'mysql' => 'database',
        'ms sql server' => 'database',

        // Anything SAP-flavoured shares one mark; the module badges elsewhere
        // carry the wordmark, so this stays a neutral glyph.
        'abap' => 'sap',
        'oo abap' => 'sap',
        'sapui5' => 'sap',
        'fiori' => 'sap',
        'sap hana' => 'sap',
        'cds views' => 'sap',
    ];

    /**
     * Icon slug for a skill name, or null when it should render as plain text.
     */
    public static function slug(?string $name): ?string
    {
        if (blank($name)) {
            return null;
        }

        return self::MAP[mb_strtolower(trim($name))] ?? null;
    }

    /**
     * True when the name belongs to the SAP family -- used to tint the chip.
     */
    public static function isSap(?string $name): bool
    {
        return self::slug($name) === 'sap';
    }
}

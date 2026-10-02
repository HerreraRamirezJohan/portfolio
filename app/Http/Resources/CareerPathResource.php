<?php

namespace App\Http\Resources;

use App\Support\TechIcon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Everything a career path page renders from config, translated into the
 * active locale. Wraps the config/portfolio.php entry plus its `key`.
 */
class CareerPathResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $path = $this->resource;
        $flow = $path['flow'];

        return [
            'key' => $path['key'],
            'number' => $path['number'],
            'nav' => __($path['nav']),
            'title' => __($path['title']),
            'lead' => __($path['lead']),
            'facts' => collect($path['facts'])
                ->map(fn (string $value, string $label) => ['label' => __($label), 'value' => __($value)])
                ->values(),
            'stack' => TechIcon::chips($path['stack']),
            'services' => collect($path['services'])
                ->map(fn (string $body, string $title) => ['title' => __($title), 'body' => __($body)])
                ->values(),
            'flow' => [
                'title' => __($flow['title']),
                'caption' => __($flow['caption']),
                'nodes' => self::nodes($flow),
                'group' => $flow['group'] ?? null,
                'tags' => array_map(fn (string $tag) => __($tag), $flow['tags']),
                'note' => __($flow['note']),
            ],
            'quote' => __($path['quote']),
            'quote_note' => __($path['quote_note']),
            'extras' => array_map(fn (array $extra) => [
                'when' => $extra['when'],
                'title' => __($extra['title']),
                'body' => __($extra['body']),
            ], $path['extras']),
        ];
    }

    /**
     * A path's flow diagram as translated nodes. Also feeds the pipelines
     * on the home page.
     *
     * @param  array<string, mixed>  $flow
     * @return list<array{layer: string, name: string, focus: bool}>
     */
    public static function nodes(array $flow): array
    {
        return collect($flow['nodes'])
            ->map(fn (array $node, int $i) => [
                'layer' => __($node[0]),
                'name' => __($node[1]),
                'focus' => $i === $flow['focus'],
            ])
            ->all();
    }
}

<?php

namespace App\Http\Resources\StructuredData\Recipe;

use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructionsResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dom = new DOMDocument();
        $dom->loadHTML($this->resource);

        $instructions = [];
        $index        = 0;
        foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            if ($node->tagName === 'h3') {
                $index++;
                $instructions[$index] = [
                    '@type'           => 'HowToSection',
                    'name'            => $node->textContent,
                    'itemListElement' => [],
                ];
                continue;
            }

            if (in_array($node->tagName, ['ul', 'ol'])) {
                foreach ($node->childNodes as $childNode) {
                    if ($childNode->nodeType !== XML_ELEMENT_NODE) {
                        continue;
                    }

                    if (isset($instructions[$index]['@type']) && $instructions[$index]['@type'] === 'HowToSection') {
                        $instructions[$index]['itemListElement'][] = [
                            '@type' => 'HowToStep',
                            'text'  => $childNode->textContent,
                        ];
                    } else {
                        $instructions[$index][] = [
                            '@type' => 'HowToStep',
                            'text'  => $childNode->textContent,
                        ];
                    }
                }
            }

            if ($node->tagName === 'p') {
                if (isset($instructions[$index]['@type']) && $instructions[$index]['@type'] === 'HowToSection') {
                    $instructions[$index]['itemListElement'][] = [
                        '@type' => 'HowToStep',
                        'text'  => $node->textContent,
                    ];
                } else {
                    $instructions[$index][] = [
                        '@type' => 'HowToStep',
                        'text'  => $node->textContent,
                    ];
                }
            }
        }

        return $instructions;
    }
}

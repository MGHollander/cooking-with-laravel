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
        $dom = new DOMDocument;
        $dom->loadHTML($this->resource);

        $instructions = [];
        $currentSectionIndex = 0;

        foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            switch ($node->tagName) {
                case 'h3':
                    $currentSectionIndex++;
                    $this->parseHeading($node, $instructions, $currentSectionIndex);
                    break;
                case 'ul':
                case 'ol':
                    $this->parseList($node, $instructions, $currentSectionIndex);
                    break;
                case 'p':
                    $this->parseParagraph($node, $instructions, $currentSectionIndex);
            }
        }

        return $instructions;
    }

    /**
     * Parse heading element and add section to instructions array.
     */
    private function parseHeading(\DOMElement $headingNode, array &$instructions, int $sectionIndex): void
    {
        $instructions[$sectionIndex] = [
            '@type' => 'HowToSection',
            'name' => $headingNode->textContent,
            'itemListElement' => [],
        ];
    }

    /**
     * Parse list elements and add to instructions array.
     */
    private function parseList(\DOMElement $listNode, array &$instructions, int $sectionIndex): void
    {
        foreach ($listNode->childNodes as $childNode) {
            if ($childNode->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $step = $this->createStep($childNode->textContent);
            $this->addItem($instructions, $sectionIndex, $step);
        }
    }

    /**
     * Parse step element (e.g., paragraph) and add to instructions array.
     */
    private function parseParagraph(\DOMElement $stepNode, array &$instructions, int $sectionIndex): void
    {
        $step = $this->createStep($stepNode->textContent);
        $this->addItem($instructions, $sectionIndex, $step);
    }

    /**
     * Create a new step array.
     */
    private function createStep(string $text): array
    {
        return [
            '@type' => 'HowToStep',
            'text' => $text,
        ];
    }

    /**
     * Add the step to the appropriate location in the instructions array.
     */
    private function addItem(array &$instructions, int $sectionIndex, array $step): void
    {
        if (isset($instructions[$sectionIndex]['@type']) && $instructions[$sectionIndex]['@type'] === 'HowToSection') {
            $instructions[$sectionIndex]['itemListElement'][] = $step;
        } else {
            $instructions[$sectionIndex][] = $step;
        }
    }
}

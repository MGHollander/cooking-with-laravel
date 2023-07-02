<?php

namespace App\Support;

use OpenAI;

class OpenAIRecipeParser
{
    public static function read($url)
    {
        if (empty(config('services.open_ai.api_key'))) {
            throw new \Exception('OpenAI API key not set');
        }

        $prompt = "Haal alleen de volgende informatie uit het recept dat je hier vindt: $url

            - titel (string) [title]
            - voorbereidingstijd (in minuten) [prepTime]
            - kooktijd (in minuten) [cookTime]
            - ingrediënten (array, 1 per regel) [ingredients]
            - stappen (array van strings) [steps]
            - porties (getal) [yield]
            - moeilijkheidsgraad (makkelijk, gemiddeld, moeilijk) [difficulty]
            - tags (komma gescheiden lijst van strings) [keywords]
            - introductie of samenvatting (string) [summary]

            Geef de output als geldige JSON terug.
            De sleutels moeten exact overeenkomen met de waarde tussen [] in de bovenstaande lijst.
            De waarden mogen alleen exacte tekst zijn zoals deze op de website staat.
            De waarden mogen ook leeg zijn als er geen informatie gevonden is.
            Vertaal de moeilijkheidsgraad naar het Engels (easy, average, difficult).
        ";

        $client = OpenAI::client(config('services.open_ai.api_key'));

        $result = $client->chat()->create([
            'model'    => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $content        = json_decode($result->choices[0]->message->content, true, 512, JSON_THROW_ON_ERROR);
        $content['url'] = $url;

        if (empty($content['title'])) {
            return null;
        }

        return $content;
    }
}

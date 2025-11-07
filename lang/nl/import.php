<?php

return [
    'title' => 'Recept importeren',
    'url' => 'URL',
    'method' => 'Methode',
    'auto' => 'Automatisch (aanbevolen)',
    'structured_data' => 'Structured data',
    'firecrawl' => 'Firecrawl',
    'ai_experimental' => 'AI (experimenteel)',
    'force_import' => 'Forceer importeren van de originele bron',
    'import_button' => 'Importeren',
    'how_does_it_work' => 'Hoe werkt dit?',
    'auto_description' => 'Probeert automatisch de beste methode te kiezen om een recept van de webpagina te halen. Dit is de aanbevolen optie omdat het de hoogste kans op succes heeft.',
    'structured_data_description' => 'Bij het importeren wordt er op een webpagina gezocht naar een recept dat is gedefinieerd in het :schema formaat. Er wordt gezocht naar Microdata, RDFa en JSON-LD markups. Als er geen recept wordt gevonden dan wordt er een foutmelding weergegeven.',
    'firecrawl_description' => 'Firecrawl is een geavanceerde web scraping service die gebruik maakt van AI om recepten van webpagina\'s te halen. Er zijn kosten verbonden aan het gebruik van Firecrawl.',
    'ai_description' => 'Met behulp van Open AI wordt er een tekst gegenereerd op basis van de inhoud van de webpagina. Deze tekst wordt vervolgens geanalyseerd om te kijken of er een recept in staat. Als er een recept wordt gevonden dan wordt deze geïmporteerd. Als er geen recept wordt gevonden dan wordt er een foutmelding weergegeven. Deze methode is experimenteel en kan dus fouten bevatten. Er zijn ook kosten verbonden aan het gebruik van Open AI.',
    'form' => [
        'title' => 'Geïmporteerd recept controleren',
        'loading' => 'Het recept wordt geïmporteerd.',
        'no_images_found' => 'Er zijn geen geldige afbeeldingen gevonden voor dit recept. Het recept wordt geïmporteerd zonder afbeelding.',
        'import_without_image' => 'Importeer zonder afbeelding',
        'save' => 'Opslaan',
        'save_and_new' => 'Opslaan en nieuw recept importeren',
    ],
];
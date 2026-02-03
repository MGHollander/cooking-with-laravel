<?php

return [
    'title' => 'Import recipe',
    'url' => 'URL',
    'method' => 'Method',
    'auto' => 'Automatic (recommended)',
    'structured_data' => 'Structured data',
    'firecrawl' => 'Firecrawl',
    'ai_experimental' => 'AI (experimental)',
    'force_import' => 'Force import from the original source',
    'import_button' => 'Import',
    'how_does_it_work' => 'How does this work?',
    'auto_description' => 'Automatically tries to choose the best method to retrieve a recipe from the web page. This is the recommended option as it has the highest chance of success.',
    'structured_data_description' => 'When importing, a web page is searched for a recipe defined in the :schema format. Microdata, RDFa and JSON-LD markups are searched. If no recipe is found, an error message is displayed.',
    'firecrawl_description' => 'Firecrawl is an advanced web scraping service that uses AI to retrieve recipes from web pages. There are costs associated with using Firecrawl.',
    'ai_description' => 'Using Open AI, a text is generated based on the content of the web page. This text is then analyzed to see if there is a recipe in it. If a recipe is found, it is imported. If no recipe is found, an error message is displayed. This method is experimental and may contain errors. There are also costs associated with using Open AI.',
    'form' => [
        'title' => 'Review imported recipe',
        'loading' => 'The recipe is being imported.',
        'no_images_found' => 'No valid images were found for this recipe. The recipe will be imported without an image.',
        'import_without_image' => 'Import without image',
        'save' => 'Save',
        'save_and_new' => 'Save and import new recipe',
    ],
    'flash' => [
        'already_imported' => 'You have already imported this recipe: <a href=":url">:title</a>',
        'imported_with_link' => 'The recipe "<a href=":url"><i>:title</i></a>" was successfully imported! 🎉',
        'imported' => 'The recipe was successfully imported! 🎉',
    ],
    'errors' => [
        'no_recipe_found' => 'Unfortunately, we could not find a recipe on this page. You can try another method. If that doesn\'t work, you will need to enter the recipe manually.',
        'url_required' => 'URL parameter is required',
    ],
];

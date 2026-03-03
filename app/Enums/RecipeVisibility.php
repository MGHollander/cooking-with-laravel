<?php

namespace App\Enums;

enum RecipeVisibility: string
{
    case Private = 'private';
    case DirectLink = 'direct_link';
    case Public = 'public';
}

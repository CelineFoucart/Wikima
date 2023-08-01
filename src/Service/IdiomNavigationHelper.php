<?php

namespace App\Service;

use App\Entity\Idiom;

final class IdiomNavigationHelper
{
    public static function generateNavigation(Idiom $idiom): array
    {
        $navigations = [];

        foreach ($idiom->getIdiomArticles() as $article) {
            $key = $article->getCategory() ? $article->getCategory()->getId() : 0;
            if (isset($navigations[$key])) {
                $navigations[$key]['articles'][] = $article;
            } else {
                $navigations[$key] = [
                    'category' => $article->getCategory() ? $article->getCategory()->getTitle() : 'Sans catégorie',
                    'articles' => [$article]
                ];
            }
        }

        return $navigations;
    }
}

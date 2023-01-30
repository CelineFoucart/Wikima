<?php

namespace App\Service;

class AlphabeticalHelperService
{
    public function formatArray(iterable $items): array
    {
        $orderedItems = [];
        
        foreach ($items as $item) {
            $title = mb_strtolower($item->getTitle());
            $title = preg_replace('#é|è|ë|ê#', 'e', $title);
            $title = preg_replace('#â|à|ä#', 'a', $title);
            $title = preg_replace('#ù|ü|û#', 'u', $title);
            $title = preg_replace('#ï|î#', 'i', $title);
            $title = preg_replace('#ô|ö#', 'o', $title);

            $letter = strtoupper(substr(trim($title, ' '), 0, 1));

            if (isset($orderedItems[$letter])) {
                $orderedItems[$letter][] = $item;
            } else {
                $orderedItems[$letter] = [$item];
            }
        }

        return $orderedItems;
    }
}
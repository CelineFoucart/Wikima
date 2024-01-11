<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\Place;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;

final class WordPlaceGenerator extends AbstractWordGenerator
{
    private Place $place;

    /**
     * Get the value of place.
     */
    public function getPlace(): Place
    {
        return $this->place;
    }

    /**
     * Set the value of place.
     */
    public function setPlace(Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function generate(): array
    {
        $this->setStyle()->setProperties();

        $section = $this->phpWord->addSection();
        if (null !== $this->place->getTypes()) {
            $section->addText(
                $this->reduceCollectionToString($this->place->getTypes()),
                ['bold' => true, 'italic' => true],
                ['spaceAfter' => Converter::cmToTwip(0)]
            );
        }

        $section->addTitle((string) $this->place, 0);
        $categories = $this->reduceCollectionToString($this->place->getCategories());
        $section->addText('Catégories : '.$categories, ['bold' => true, 'italic' => true]);
        $portals = $this->reduceCollectionToString($this->place->getPortals());
        $section->addText('Portails : '.$portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        HTML::addHtml($section, $this->place->getDescription());

        $tableData = self::getTableData($this->place, $this->reduceCollectionToString($this->place->getLocalisations()));
        if (!empty($tableData)) {
            $table = $section->addTable($this->getDefaultTableStyle());
            foreach ($tableData as $key => $value) {
                $table->addRow();
                $table->addCell(null, ['valign' => 'center'])->addText($key, ['bold' => true], ['spaceAfter' => Converter::cmToTwip(0)]);
                $table->addCell(null, ['valign' => 'center'])->addText($value, [], ['spaceAfter' => Converter::cmToTwip(0)]);
            }
        }

        $section->addTitle('Présentation', 1);
        HTML::addHtml($section, $this->place->getPresentation());
        $section->addTitle('Histoire', 1);
        HTML::addHtml($section, $this->place->getHistory());
        $section->addTitle('Lieux associés', 1);
        foreach ($this->place->getPlaces() as $child) {
            $section->addTitle($child->getTitle(), 2);
            HTML::addHtml($section, $child->getDescription());
        }

        return $this->saveFile($this->place->getSlug());
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => (string) $this->place,
            'description' => $this->place->getDescription() ? $this->place->getDescription() : '',
            'category' => 'Lieu',
            'subject' => 'Worldbuilding',
        ];
    }

    public static function getTableData(Place $place, string $localisations): array
    {
        if ($place->getSituation()) {
            $tableValues['Situation'] = $place->getSituation();
        }

        if ($place->getDominatedBy()) {
            $tableValues['Controlé par'] = $place->getDominatedBy();
        }

        if ($place->getIsInhabitable()) {
            $tableValues['Habitable'] = $place->getIsInhabitable();
        }

        if ($place->getCapitaleCity()) {
            $tableValues['Capitale'] = $place->getCapitaleCity();
        }

        if ($place->getLanguages()) {
            $tableValues['Langues'] = $place->getLanguages();
        }

        if (!$place->getLocalisations()->isEmpty()) {
            $tableValues['Localisations'] = $localisations;
        }

        if ($place->getPopulation()) {
            $tableValues['Population'] = $place->getPopulation();
        }

        if ($place->getSize()) {
            $tableValues['Taille'] = $place->getSize();
        }

        return $tableValues;
    }
}

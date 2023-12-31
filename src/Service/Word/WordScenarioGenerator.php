<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\Scenario;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use App\Service\Word\AbstractWordGenerator;

final class WordScenarioGenerator extends AbstractWordGenerator
{
    private Scenario $scenario;

    /**
     * Get the value of scenario.
     */
    public function getScenario(): Scenario
    {
        return $this->scenario;
    }

    /**
     * Set the value of scenario.
     */
    public function setScenario(Scenario $scenario): self
    {
        $this->scenario = $scenario;

        return $this;
    }

    public function generate(): array
    {
        $this->setStyle()->setProperties();

        $section = $this->phpWord->addSection();
        $section->addTitle($this->scenario->getTitle(), 0);

        return $this->appendScenarioIntroduction($section)->appendEpisodes($section)->saveFile('synopsis-'.$this->scenario->getSlug());
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => $this->scenario->getTitle(),
            'category' => 'Scénario',
            'created' => $this->scenario->getCreatedAt()->getTimestamp(),
            'description' => $this->scenario->getPitch() ? $this->scenario->getPitch() : '',
            'modified' => $this->scenario->getUpdatedAt() ? $this->scenario->getUpdatedAt()->getTimestamp() : null,
        ];
    }

    /**
     * Append to the file the scenario general informations.
     * 
     * @param Section $section
     * 
     * @return static
     */
    private function appendScenarioIntroduction(Section $section): static
    {
        if (!$this->scenario->getPortals()->isEmpty()) {
            $portals = "Sections de l'encyclopédie : ".$this->reduceCollectionToString($this->scenario->getPortals());
            $section->addText($portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        }

        if (!$this->scenario->getCategories()->isEmpty()) {
            $categories = 'Genres : '.$this->reduceCollectionToString($this->scenario->getCategories());
            $section->addText($categories, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        }

        if ($this->scenario->getPitch()) {
            $textWithBreakLines = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $this->scenario->getPitch());
            $section->addText(
                'Pitch : '.$textWithBreakLines,
                ['bold' => true, 'italic' => true],
                ['spaceAfter' => Converter::cmToTwip(0.6)]
            );
        }

        HTML::addHtml($section, $this->scenario->getDescription());

        return $this;
    }

    /**
     * Append to the file the episodes.
     * 
     * @param Section $section
     * 
     * @return static
     */
    private function appendEpisodes(Section $section): static
    {
        foreach ($this->scenario->getEpisodes() as $episode) {
            if ((bool) $episode->isArchived() === true) {
                continue;
            }

            $section->addTitle($episode->getTitle(), 1);

            if (!$episode->getPersons()->isEmpty()) {
                $personList = 'Personnages associés : '.$this->reduceCollectionToString($episode->getPersons());
                $section->addText($personList, ['bold' => true, 'italic' => true]);
            }

            if (!$episode->getPlaces()->isEmpty()) {
                $placeList = 'Lieux associés : '.$this->reduceCollectionToString($episode->getPlaces());
                $section->addText($placeList, ['bold' => true, 'italic' => true]);
            }

            HTML::addHtml($section, $episode->getContent());
        }

        return $this;
    }
}

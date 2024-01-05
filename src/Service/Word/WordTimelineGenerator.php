<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\Timeline;
use PhpOffice\PhpWord\Shared\Converter;

final class WordTimelineGenerator extends AbstractWordGenerator
{
    private Timeline $timeline;

    /**
     * Get the value of place.
     */
    public function getTimeline(): Timeline
    {
        return $this->timeline;
    }

    /**
     * Set the value of place.
     */
    public function setTimeline(Timeline $timeline): self
    {
        $this->timeline = $timeline;

        return $this;
    }

    public function generate(): array
    {
        $this->setStyle(30, 15, 12)->setProperties();

        $section = $this->phpWord->addSection();
        $section->addTitle((string) $this->timeline, 0);
        $categories = $this->reduceCollectionToString($this->timeline->getCategories());
        $section->addText('Catégories : '.$categories, ['bold' => true, 'italic' => true]);
        $portals = $this->reduceCollectionToString($this->timeline->getPortals());
        $section->addText('Portails : '.$portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);

        $descriptionWithBreakLines = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $this->timeline->getDescription());
        $section->addText($descriptionWithBreakLines);

        foreach ($this->timeline->getEvents() as $event) {
            $section->addTitle($event->getTitle(), 1);
            $section->addText($event->getDuration(), ['bold' => true, 'italic' => true]);
            $presentationWithBreakLines = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $event->getPresentation());
            $section->addText($presentationWithBreakLines);
        }

        return $this->saveFile('chronologie-' . $this->timeline->getSlug());
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => (string) $this->timeline,
            'description' => $this->timeline->getDescription() ? $this->timeline->getDescription() : '',
            'category' => 'Chronologie',
            'subject' => 'Worldbuilding',
            'created' => $this->timeline->getCreatedAt()->getTimestamp(),
            'modified' => $this->timeline->getUpdatedAt() ? $this->timeline->getUpdatedAt()->getTimestamp() : null,
        ];
    }
}

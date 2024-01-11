<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\Portal;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;
use App\Service\Word\AbstractWordGenerator;
use PhpOffice\PhpWord\Element\Section;

final class WordPortalGenerator extends AbstractWordGenerator
{
    private Portal $portal;

    /**
     * Get the value of portal
     *
     * @return Portal
     */
    public function getPortal(): Portal
    {
        return $this->portal;
    }

    /**
     * Set the value of portal
     *
     * @param Portal $portal
     *
     * @return self
     */
    public function setPortal(Portal $portal): self
    {
        $this->portal = $portal;

        return $this;
    }

    public function generate(string $type = 'article'): array
    {
        $this->setStyle(h1Size:35, h2Size:22, h3Size:16)->setProperties();

        $section = $this->phpWord->addSection();
        $section->addTitle($this->portal->getTitle(), 0);
        $categories = $this->reduceCollectionToString($this->portal->getCategories());
        $section->addText($categories, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        HTML::addHtml($section, $this->portal->getPresentation());

        switch ($type) {
            case 'article':
                $this->generateForArticles();
                break;
            case 'timeline':
                $this->generateForTimelines();
                break;
            case 'person':
                $this->generateForPersons();
                break;
            case 'place':
                $this->generateForPlaces();
                break;
            default:
                $this->generateForArticles();
                break;
        }

        $this->appendSummary($section);

        return $this->saveFile($type . '-' . $this->portal->getSlug());
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => $this->portal->getTitle(),
            'description' => $this->portal->getDescription() ? $this->portal->getDescription() : '',
            'category' => 'Worldbuilding',
            'created' => $this->portal->getCreatedAt()->getTimestamp(),
            'modified' => $this->portal->getUpdatedAt() ? $this->portal->getUpdatedAt()->getTimestamp() : null,
            'keywords' => $this->portal->getKeywords() ? $this->portal->getKeywords() : '',
            'subject' => 'Worldbuilding',
        ];
    }

    private function generateForArticles(): void
    {
        foreach ($this->portal->getArticles() as $article) {
            if ($article->getIsArchived() === true || $article->getIsDraft() === true || $article->getIsPrivate() === true) {
                continue;
            }

            $subSection = $this->phpWord->addSection();
            $subSection->addTitle($article->getTitle(), 1);
            HTML::addHtml($subSection, $article->getContent());

            foreach ($article->getSections() as $part) {
                $subSection->addTitle($part->getTitle(), 2);
                HTML::addHtml($subSection, $part->getContent());
            }
        }
    }

    private function generateForTimelines(): void
    {
        foreach ($this->portal->getTimelines() as $timeline) {
            $subSection = $this->phpWord->addSection();
            $subSection->addTitle((string) $timeline, 1);
            $descriptionWithBreakLines = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $timeline->getDescription());
            $subSection->addText($descriptionWithBreakLines);

            foreach ($timeline->getEvents() as $event) {
                $subSection->addTitle($event->getTitle(), 2);
                $subSection->addText($event->getDuration(), ['bold' => true, 'italic' => true]);
                $presentationWithBreakLines = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $event->getPresentation());
                $subSection->addText($presentationWithBreakLines);
            }
        }
    }

    private function generateForPersons(): void
    {
        foreach ($this->portal->getPeople() as $person) {
            if ($person->getIsArchived()) {
                continue;
            }

            $subSection = $this->phpWord->addSection();
            $subSection->addTitle((string) $person, 1);

            if (!$person->getType()->isEmpty()) {
                $subSection->addText(
                    $this->reduceCollectionToString($person->getType()),
                    ['bold' => true, 'italic' => true],
                    ['spaceAfter' => Converter::cmToTwip(0.6)]
                );
            }

            HTML::addHtml($subSection, $person->getPresentation());
            
            $table = $subSection->addTable($this->getDefaultTableStyle());
            $tableData = WordPersonGenerator::getTableData($person);
            foreach ($tableData as $key => $value) {
                $table->addRow();
                $table->addCell(null, ['valign' => 'center'])->addText($key, ['bold' => true], ['spaceAfter' => Converter::cmToTwip(0)]);
                $table->addCell(null, ['valign' => 'center'])->addText($value, [], ['spaceAfter' => Converter::cmToTwip(0)]);
            }

            if ($person->getBiography()) {
                $subSection->addTitle('Biographie', 2);
                HTML::addHtml($subSection, $person->getBiography());
            }

            if ($person->getPersonality()) {
                $subSection->addTitle('Personnalité', 2);
                HTML::addHtml($subSection, $person->getPersonality());
            }
        }
    }

    private function generateForPlaces(): void
    {
        foreach ($this->portal->getPlaces() as $place) {
            if ($place->getIsArchived()) {
                continue;
            }

            $subSection = $this->phpWord->addSection();
            $subSection->addTitle((string) $place, 1);

            if (!$place->getTypes()->isEmpty()) {
                $subSection->addText(
                    $this->reduceCollectionToString($place->getTypes()),
                    ['bold' => true, 'italic' => true],
                    ['spaceAfter' => Converter::cmToTwip(0.6)]
                );
            }

            HTML::addHtml($subSection, $place->getDescription());

            $tableData = WordPlaceGenerator::getTableData($place, $this->reduceCollectionToString($place->getLocalisations()));
            if (!empty($tableData)) {
                $table = $subSection->addTable($this->getDefaultTableStyle());
                foreach ($tableData as $key => $value) {
                    $table->addRow();
                    $table->addCell(null, ['valign' => 'center'])->addText($key, ['bold' => true], ['spaceAfter' => Converter::cmToTwip(0)]);
                    $table->addCell(null, ['valign' => 'center'])->addText($value, [], ['spaceAfter' => Converter::cmToTwip(0)]);
                }
            }

            if ($place->getPresentation()) {
                $subSection->addTitle('Présentation', 2);
                HTML::addHtml($subSection, $place->getPresentation());
            }

            if ($place->getHistory()) {
                $subSection->addTitle('Histoire', 2);
                HTML::addHtml($subSection, $place->getHistory());
            }

            if (!$place->getPlaces()->isEmpty()) {
                $subSection->addTitle('Lieux associés', 2);
                foreach ($place->getPlaces() as $child) {
                    $subSection->addTitle($child->getTitle(), 3);
                    HTML::addHtml($subSection, $child->getDescription());
                }
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\Idiom;
use PhpOffice\PhpWord\Shared\Html;
use App\Service\IdiomNavigationHelper;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

final class WordIdiomGenerator extends AbstractWordGenerator
{
    private Idiom $idiom;

    /**
     * Get the value of idiom
     *
     * @return Idiom
     */
    public function getIdiom(): Idiom
    {
        return $this->idiom;
    }

    /**
     * Set the value of idiom
     *
     * @param Idiom $idiom
     *
     * @return self
     */
    public function setIdiom(Idiom $idiom): self
    {
        $this->idiom = $idiom;

        return $this;
    }
    public function generate(): array
    {
        $this->setStyle(h1Size:35, h2Size:25, h3Size:18)->setProperties();
        $section = $this->phpWord->addSection();
        $this->setFirstPageContent($section)->appendIntroductionPart()->appendPartsFile()->appendSummary($section);

        return $this->saveFile($this->idiom->getSlug());
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => $this->idiom->getTranslatedName(),
            'description' => $this->idiom->getDescription() ? $this->idiom->getDescription() : '',
            'category' => 'Conlang',
            'created' => $this->idiom->getCreatedAt()->getTimestamp(),
            'modified' => $this->idiom->getUpdatedAt() ? $this->idiom->getUpdatedAt()->getTimestamp() : null,
            'subject' => 'Worldbuilding'
        ];
    }

    private function setFirstPageContent(Section $section): static
    {
        $section->addTitle($this->idiom->getTranslatedName(), 0);

        if ($this->idiom->getOriginalName()) {
            $section->addText(
                'Nom original : ' . $this->idiom->getOriginalName(),
                ['bold' => true, 'italic' => true],
                ['spaceAfter' => Converter::cmToTwip(0)]
            );
        }

        $portals = $this->reduceCollectionToString($this->idiom->getPortals());
        $section->addText('Portails : ' . $portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        $section->addText($this->idiom->getDescription());

        return $this;
    }

    private function appendIntroductionPart(): static
    {
        if ($this->idiom->getPresentation()) {
            $intro = $this->phpWord->addSection();
            $intro->addTitle('1. Introduction', 1);
            HTML::addHtml($intro, $this->idiom->getPresentation());
        }

        return $this;
    }

    private function appendPartsFile(): static
    {
        $parts = IdiomNavigationHelper::generateNavigation($this->idiom);
        $starting = ($this->idiom->getPresentation()) ? 2 : 1;

        foreach ($parts as $category) {
            $subSection = $this->phpWord->addSection();
            $subSection->addTitle($starting . '. ' . $category['category'], 1);

            foreach ($category['articles'] as $article) {
                $subSection->addTitle($article->getTitle(), 2);
                HTML::addHtml($subSection, $article->getContent());
            }

            $starting++;
        }

        return $this;
    }

}

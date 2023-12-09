<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Scenario;
use Doctrine\Common\Collections\Collection;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Shared\Converter;

class WordGenerator
{
    private Article $article;

    private Scenario $scenario;
    
    public function __construct(private $tmpDir)
    {
        \PhpOffice\PhpWord\Settings::setZipClass(\PhpOffice\PhpWord\Settings::PCLZIP);
    }

    /**
     * Generate a word file for an entity Scenario.
     * 
     * @return array
     */
    public function generateFileScenario(): array
    {
        $phpWord = new PhpWord();
        $this->setStyle($phpWord);
        $properties = $phpWord->getDocInfo();
        $properties
            ->setTitle($this->scenario->getTitle())
            ->setCategory('Scénario')
            ->setCreated($this->scenario->getCreatedAt()->getTimestamp())
            ->setDescription($this->scenario->getPitch() ? $this->scenario->getPitch() : '');

        if ($this->scenario->getUpdatedAt()) {
            $properties->setModified($this->scenario->getUpdatedAt()->getTimestamp());
        }

        $section = $phpWord->addSection();
        $section->addTitle($this->scenario->getTitle(), 0);
        
        if (!$this->scenario->getPortals()->isEmpty()) {
            $portals = "Sections de l'encyclopédie : " . $this->reduceCollectionToString($this->scenario->getPortals());
            $section->addText($portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        }

        if (!$this->scenario->getCategories()->isEmpty()) {
            $categories = 'Genres : ' . $this->reduceCollectionToString($this->scenario->getCategories());
            $section->addText($categories, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        }

        if ($this->scenario->getPitch()) {
            $textWithBreakLines = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $this->scenario->getPitch());
            $section->addText(
                'Pitch : ' . $textWithBreakLines, 
                ['bold' => true, 'italic' => true], 
                ['spaceAfter' => Converter::cmToTwip(0.6)]
            );
        }

        HTML::addHtml($section, $this->scenario->getDescription());

        foreach ($this->scenario->getEpisodes() as $episode) {
            $section->addTitle($episode->getTitle(), 1);

            if (!$episode->getPersons()->isEmpty()) {
                $personList = 'Personnages associés : ' . $this->reduceCollectionToString($episode->getPersons());
                $section->addText($personList, ['bold' => true, 'italic' => true]);
            }

            if (!$episode->getPlaces()->isEmpty()) {
                $placeList = 'Lieux associés : ' . $this->reduceCollectionToString($episode->getPlaces());
                $section->addText($placeList, ['bold' => true, 'italic' => true]);
            }

            HTML::addHtml($section, $episode->getContent());
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $filename = 'synopsis-' . $this->scenario->getSlug() . '.docx';
        $objWriter->save($this->tmpDir . DIRECTORY_SEPARATOR . $filename);

        return [
            'path' => $this->tmpDir . DIRECTORY_SEPARATOR . $filename,
            'filename' => $filename
        ];
    }

    /**
     * Generate a word file for an entity Article.
     * 
     * @return array
     */
    public function generateFileArticle(): array
    {
        $phpWord = new PhpWord();
        $this->setStyle($phpWord);
        $properties = $phpWord->getDocInfo();
        $properties->setTitle($this->article->getTitle());
        $properties->setDescription($this->article->getDescription() ? $this->article->getDescription() : '');
        $properties->setCategory('Element de worldbuilding');
        $properties->setCreated($this->article->getCreatedAt()->getTimestamp());

        if ($this->article->getUpdatedAt()) {
            $properties->setModified($this->article->getUpdatedAt()->getTimestamp());
        }
        $properties->setKeywords($this->article->getKeywords() ? $this->article->getKeywords() : '');
        $properties->setSubject("Worldbuilding");
        
        $section = $phpWord->addSection();

        if ($this->article->getType() !== null) {
            $section->addText(
                $this->article->getType()->getTitle(), 
                ['bold' => true, 'italic' => true], 
                ['spaceAfter' => Converter::cmToTwip(0)]
            );
        }

        $section->addTitle($this->article->getTitle(), 0);
        $portals = $this->reduceCollectionToString($this->article->getPortals());
        $section->addText($portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
        HTML::addHtml($section, $this->article->getContent());

        $subSection = $phpWord->addSection();
        foreach ($this->article->getSections() as $part) {
            $subSection->addTitle($part->getTitle(), 1);
            HTML::addHtml($subSection, $part->getContent());
        }

        $section->addText('Sommaire :', ['bold' => true, 'smallCaps' => true], ['spaceBefore' => Converter::cmToTwip(0.6)]);
        $section->addTOC(['bold' => true], ['indent' => Converter::cmToTwip(0.5)], 1, 9);
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $filename = $this->article->getSlug() . '.docx';
        $objWriter->save($this->tmpDir . DIRECTORY_SEPARATOR . $filename);

        return [
            'path' => $this->tmpDir . DIRECTORY_SEPARATOR . $filename,
            'filename' => $filename
        ];
    }

    /**
     * Get the value of article
     *
     * @return Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }

    /**
     * Set the value of article
     *
     * @param Article $article
     *
     * @return self
     */
    public function setArticle(Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get the value of scenario
     *
     * @return Scenario
     */
    public function getScenario(): Scenario
    {
        return $this->scenario;
    }

    /**
     * Set the value of scenario
     *
     * @param Scenario $scenario
     *
     * @return self
     */
    public function setScenario(Scenario $scenario): self
    {
        $this->scenario = $scenario;

        return $this;
    }

    /**
     * Define the style.
     * 
     * @param PhpWord $phpWord
     * 
     * @return PhpWord
     */
    private function setStyle(PhpWord $phpWord): PhpWord
    {
        $phpWord->addTitleStyle(0, ['size' => 30, 'bold' => true]);
        $phpWord->addTitleStyle(1, ['size' => 18,  'bold' => true]);
        $phpWord->addTitleStyle(2, ['size' => 15,  'bold' => true]);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::FR_FR));

        return $phpWord;
    }

    /**
     * Reduce a collection of entity with the _toString implemented.
     * 
     * @param Collection $elements
     * 
     * @return string
     */
    private function reduceCollectionToString(Collection $elements): string
    {
        $titles = array_map(fn($value): string => $value, $elements->toArray());

        return join(', ', $titles);
    }
}

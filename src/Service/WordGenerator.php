<?php

namespace App\Service;

use App\Entity\Article;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Shared\Converter;

class WordGenerator
{
    private Article $article;
    
    public function __construct(private $tmpDir)
    {
        \PhpOffice\PhpWord\Settings::setZipClass(\PhpOffice\PhpWord\Settings::PCLZIP);
    }

    public function generateFileArticle(): array
    {
        $phpWord = new PhpWord();
        $this->setStyle($phpWord);
        $this->setProperties($phpWord);
        
        $section = $phpWord->addSection();
        $section->addTitle($this->article->getTitle(), 0);
        $portals = array_map(fn($value): string => $value->getTitle(), $this->article->getPortals()->toArray());
        $section->addText(join(', ', $portals), ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);
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
     * Set document properties.
     * 
     * @param PhpWord $phpWord
     * 
     * @return PhpWord
     */
    private function setProperties(PhpWord $phpWord): PhpWord
    {
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

        return $phpWord;
    }
}

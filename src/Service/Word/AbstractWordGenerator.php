<?php

declare(strict_types=1);

namespace App\Service\Word;

use Doctrine\Common\Collections\Collection;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Language;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractWordGenerator
{
    protected PhpWord $phpWord;

    protected string $uploadDir;

    public function __construct(protected $tmpDir, protected string $publicDir, protected UrlGeneratorInterface $urlGenerator)
    {
        Settings::setZipClass(Settings::PCLZIP);
        $this->phpWord = new PhpWord();
        $this->uploadDir = $this->publicDir.'/uploads/';
    }

    /**
     * Generate a word file for an entity.
     */
    abstract public function generate(): array;

    /**
     * Get an array of properties for the file with keys: title, description, category, created, modified, keywords and subject.
     */
    abstract protected function getParamProperties(): array;

    /**
     * Set the properties of the file with the array returned by the method getParamProperties.
     * The the key title and created are mandatory.
     */
    protected function setProperties(): static
    {
        $params = $this->getParamProperties();
        $properties = $this->phpWord->getDocInfo();
        $properties
            ->setTitle($params['title'])
            ->setDescription(isset($params['description']) ? $params['description'] : '')
            ->setCategory(isset($params['category']) ? $params['category'] : '')
            ->setCreated(isset($params['created']) ? $params['created'] : null)
            ->setModified(isset($params['modified']) ? $params['modified'] : null)
            ->setKeywords(isset($params['keywords']) ? $params['keywords'] : '')
            ->setSubject(isset($params['subject']) ? $params['subject'] : '');

        return $this;
    }

    /**
     * Define the style.
     */
    protected function setStyle(int $h1Size = 30, int $h2Size = 18, int $h3Size = 14): static
    {
        $this->phpWord->addTitleStyle(0, ['size' => $h1Size, 'bold' => true]);
        $this->phpWord->addTitleStyle(1, ['size' => $h2Size,  'bold' => true], ['spaceBefore' => Converter::cmToTwip(0.5)]);
        $this->phpWord->addTitleStyle(2, ['size' => $h3Size,  'bold' => true]);
        $this->phpWord->addTitleStyle(3, ['size' => 13,  'bold' => true]);
        $this->phpWord->setDefaultFontName('Times New Roman');
        $this->phpWord->setDefaultFontSize(12);
        $this->phpWord->getSettings()->setThemeFontLang(new Language(Language::FR_FR));

        return $this;
    }

    /**
     * Reduce a collection of entity with the _toString implemented.
     */
    protected function reduceCollectionToString(Collection $elements): string
    {
        $titles = array_map(fn ($value): string => (string) $value, $elements->toArray());

        return join(', ', $titles);
    }

    protected function appendSummary(Section $section): static
    {
        $section->addText('Sommaire :', ['bold' => true, 'smallCaps' => true], ['spaceBefore' => Converter::cmToTwip(0.6)]);
        $section->addTOC(['bold' => true], ['indent' => Converter::cmToTwip(0.5)], 1, 9);

        return $this;
    }

    /**
     * Save the file and return the fil info (the path and the filename).
     */
    protected function saveFile(string $filename): array
    {
        $objWriter = IOFactory::createWriter($this->phpWord, 'Word2007');
        $objWriter->save($this->tmpDir.DIRECTORY_SEPARATOR.$filename.'.docx');

        return [
            'path' => $this->tmpDir.DIRECTORY_SEPARATOR.$filename.'.docx',
            'filename' => $filename.'.docx',
        ];
    }

    protected function getDefaultTableStyle(): array
    {
        return [
            'borderColor' => '000000',
            'borderSize' => 1,
            'cellMargin' => Converter::cmToTwip(0.09),
            'unit' => TblWidth::PERCENT,
            'width' => 100 * 50,
        ];
    }

    protected function getImageDefaultStyles(): array
    {
        return [
            'width' => 450,
            'alignment' => Jc::CENTER,
            'wrappingStyle' => 'inline',
        ];
    }
}

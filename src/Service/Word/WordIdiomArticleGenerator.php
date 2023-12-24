<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\IdiomArticle;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;

final class WordIdiomArticleGenerator extends AbstractWordGenerator
{
    private IdiomArticle $article;

    /**
     * Get the value of article.
     */
    public function getArticle(): IdiomArticle
    {
        return $this->article;
    }

    /**
     * Set the value of article.
     */
    public function setArticle(IdiomArticle $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function generate(): array
    {
        $this->setStyle()->setProperties();
        $section = $this->phpWord->addSection();

        if (null !== $this->article->getCategory()) {
            $section->addText(
                $this->article->getCategory()->getTitle(),
                ['bold' => true, 'italic' => true],
                ['spaceAfter' => Converter::cmToTwip(0)]
            );
        }

        $section->addTitle($this->article->getTitle(), 0);
        $section->addText(
            $this->getArticle()->getIdiom()->getTranslatedName(),
            ['bold' => true, 'italic' => true],
            ['spaceAfter' => Converter::cmToTwip(0.6),
        ]);

        HTML::addHtml($section, $this->article->getContent());
        $filename = $this->article->getIdiom()->getSlug().'-'.$this->article->getSlug();

        return $this->saveFile($filename);
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => $this->article->getTitle(),
            'description' => $this->article->getDescription() ? $this->article->getDescription() : '',
            'category' => $this->article->getIdiom()->getTranslatedName(),
            'created' => $this->article->getCreatedAt()->getTimestamp(),
            'modified' => $this->article->getUpdatedAt() ? $this->article->getUpdatedAt()->getTimestamp() : null,
            'subject' => 'Conlang',
        ];
    }
}

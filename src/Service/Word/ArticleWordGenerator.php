<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\Article;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;

final class ArticleWordGenerator extends AbstractWordGenerator
{
    private Article $article;

    /**
     * Get the value of article.
     */
    public function getArticle(): Article
    {
        return $this->article;
    }

    /**
     * Set the value of article.
     */
    public function setArticle(Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function generate(): array
    {
        $this->setStyle()->setProperties();

        $section = $this->phpWord->addSection();
        if (null !== $this->article->getType()) {
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

        $subSection = $this->phpWord->addSection();
        foreach ($this->article->getSections() as $part) {
            $subSection->addTitle($part->getTitle(), 1);
            HTML::addHtml($subSection, $part->getContent());
        }

        $this->appendSummary($section);

        return $this->saveFile($this->article->getSlug());

        
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => $this->article->getTitle(),
            'description' => $this->article->getDescription() ? $this->article->getDescription() : '',
            'category' => 'Element de worldbuilding',
            'created' => $this->article->getCreatedAt()->getTimestamp(),
            'modified' => $this->article->getUpdatedAt() ? $this->article->getUpdatedAt()->getTimestamp() : null,
            'keywords' => $this->article->getKeywords() ? $this->article->getKeywords() : '',
            'subject' => 'Worldbuilding',
        ];
    }
}

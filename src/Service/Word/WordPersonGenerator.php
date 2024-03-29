<?php

declare(strict_types=1);

namespace App\Service\Word;

use App\Entity\Person;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;

final class WordPersonGenerator extends AbstractWordGenerator
{
    private Person $person;

    /**
     * Get the value of person.
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * Set the value of person.
     */
    public function setPerson(Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function generate(): array
    {
        $this->setStyle(h3Size: 13)->setProperties();

        $section = $this->phpWord->addSection();
        if (null !== $this->person->getType()) {
            $section->addText(
                $this->reduceCollectionToString($this->person->getType()),
                ['bold' => true, 'italic' => true],
                ['spaceAfter' => Converter::cmToTwip(0)]
            );
        }

        $section->addTitle((string) $this->person, 0);
        $categories = $this->reduceCollectionToString($this->person->getCategories());
        $section->addText('Catégories : '.$categories, ['bold' => true, 'italic' => true]);
        $portals = $this->reduceCollectionToString($this->person->getPortals());
        $section->addText('Portails : '.$portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);

        if (null !== $this->person->getImage()) {
            $filepath = $this->uploadDir.$this->person->getImage()->getFilename();
            $section->addImage($filepath, $this->getImageDefaultStyles());
        }

        HTML::addHtml($section, $this->person->getPresentation());

        $table = $section->addTable($this->getDefaultTableStyle());
        $tableData = self::getTableData($this->person);
        foreach ($tableData as $key => $value) {
            $table->addRow();
            $table->addCell(null, ['valign' => 'center'])->addText($key, ['bold' => true], ['spaceAfter' => Converter::cmToTwip(0)]);
            $table->addCell(null, ['valign' => 'center'])->addText($value, [], ['spaceAfter' => Converter::cmToTwip(0)]);
        }

        if (null !== $this->person->getBiography()) {
            $section->addTitle('Biographie', 1);
            HTML::addHtml($section, $this->person->getBiography());
        }

        if (null !== $this->person->getPersonality()) {
            $section->addTitle('Personnalité', 1);
            HTML::addHtml($section, $this->person->getPersonality());
        }

        if (!$this->person->getLinkedPersons()->isEmpty()) {
            $section->addTitle('Relations', 1);
            foreach ($this->person->getLinkedPersons() as $relation) {
                $section->addTitle((string) $relation, 2);
                $textWithBreakLines = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $relation->getDescription());
                $section->addText($textWithBreakLines);
            }
        }

        return $this->saveFile($this->person->getSlug());
    }

    protected function getParamProperties(): array
    {
        return [
            'title' => (string) $this->person,
            'description' => $this->person->getDescription() ? $this->person->getDescription() : '',
            'category' => 'Personnage',
            'subject' => 'Worldbuilding',
        ];
    }

    public static function getTableData(Person $person): array
    {
        $tableValues = ['Nom complet' => $person->getFullname()];

        if ($person->getSpecies()) {
            $tableValues['Espèce'] = $person->getSpecies();
        }

        if ($person->getGender()) {
            $tableValues['Genre'] = $person->getGender();
        }

        if ($person->getNationality()) {
            $tableValues['Nationalité'] = $person->getNationality();
        }

        if ($person->getBirthday()) {
            $tableValues['Naissance'] = $person->getBirthday();
        }

        if ($person->getBirthdayPlace()) {
            $tableValues['Lieu de naissance'] = $person->getBirthdayPlace();
        }

        if ($person->getDeathDate()) {
            $tableValues['Mort'] = $person->getDeathDate();
        }

        if ($person->getDeathPlace()) {
            $tableValues['Lieu de mort'] = $person->getDeathPlace();
        }

        if ($person->getParents()) {
            $tableValues['Parents'] = $person->getParents();
        }

        if ($person->getSiblings()) {
            $tableValues['Fratrie'] = $person->getSiblings();
        }

        if ($person->getJob()) {
            $tableValues['Profession'] = $person->getJob();
        }

        if ($person->getPhysicalDescription()) {
            $tableValues['Physique'] = $person->getPhysicalDescription();
        }

        return $tableValues;
    }
}

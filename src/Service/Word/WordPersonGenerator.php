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
     * Get the value of person
     *
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * Set the value of person
     *
     * @param Person $person
     *
     * @return self
     */
    public function setPerson(Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function generate(): array
    {
        $this->setStyle()->setProperties();

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
        $section->addText('Catégories : ' . $categories, ['bold' => true, 'italic' => true]);
        $portals = $this->reduceCollectionToString($this->person->getPortals());
        $section->addText('Portails : ' . $portals, ['bold' => true, 'italic' => true], ['spaceAfter' => Converter::cmToTwip(0.6)]);

        HTML::addHtml($section, $this->person->getPresentation());

        $table = $section->addTable($this->getDefaultTableStyle());
        $tableData = $this->getTableData();
        foreach ($tableData as $key => $value) {
            $table->addRow();
            $table->addCell(null, ['valign' => 'center'])->addText($key, ['bold' => true], ['spaceAfter' => Converter::cmToTwip(0)]);
            $table->addCell(null, ['valign' => 'center'])->addText($value, [], ['spaceAfter' => Converter::cmToTwip(0)]);
        }

        $section->addTitle('Biographie', 1);
        HTML::addHtml($section, $this->person->getBiography());
        $section->addTitle('Personnalité', 1);
        HTML::addHtml($section, $this->person->getPersonality());

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

    private function getTableData(): array
    {
        $tableValues = ['Nom complet' => $this->person->getFullname()];

        if ($this->person->getSpecies()) {
            $tableValues['Espèce'] = $this->person->getSpecies();
        }

        if ($this->person->getGender()) {
            $tableValues['Genre'] = $this->person->getGender();
        }

        if ($this->person->getNationality()) {
            $tableValues['Nationalité'] = $this->person->getNationality();
        }

        if ($this->person->getBirthday()) {
            $tableValues['Naissance'] = $this->person->getBirthday();
        }

        if ($this->person->getBirthdayPlace()) {
            $tableValues['Lieu de naissance'] = $this->person->getBirthdayPlace();
        }

        if ($this->person->getDeathDate()) {
            $tableValues['Mort'] = $this->person->getDeathDate();
        }

        if ($this->person->getDeathPlace()) {
            $tableValues['Lieu de mort'] = $this->person->getDeathPlace();
        }

        if ($this->person->getParents()) {
            $tableValues['Parents'] = $this->person->getParents();
        }

        if ($this->person->getSiblings()) {
            $tableValues['Fratrie'] = $this->person->getSiblings();
        }

        if ($this->person->getJob()) {
            $tableValues['Profession'] = $this->person->getJob();
        }

        if ($this->person->getPhysicalDescription()) {
            $tableValues['Physique'] = $this->person->getPhysicalDescription();
        }

        return $tableValues;
    }
}

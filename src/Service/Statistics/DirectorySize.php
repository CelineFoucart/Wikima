<?php

declare(strict_types=1);

namespace App\Service\Statistics;

final class DirectorySize
{
    public const SUFFIXES = ['', 'Ko', 'Mo', 'Go', 'To'];
    
    public const EMPTY_VALUE = '0 Ko';

    public const BASE_CALCUL = 1024;

    private array $directories = [];

    public function __construct(private string $projectDir)
    {
        
    }

    /**
     * Append a directory size.
     * 
     * @param string $directory          the path to the directory, for example /public/uploads
     * @throws \UnexpectedValueException if the directory cannot be opened
     * @throws \ValueError               if the directory is empty
     * 
     * @return static
     */
    public function addDirectory(string $directory): static
    {
        $dirIterator = new \DirectoryIterator($this->projectDir . $directory);
        $totalSize = 0;

        foreach ($dirIterator as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }

            $totalSize += ((int)$file->getSize());
        }

        $this->directories[] = $totalSize;

        return $this;
    }

    /**
     * @throws \ValueError  if directories is empty
     * 
     * @return int
     */
    public function getTotalSize(): int
    {
        if (empty($this->directories)) {
            throw new \ValueError("No directory");
        }

        return array_sum($this->directories);
    }

    /**
     * @param int $precision
     * @throws \ValueError  if directories is empty
     * 
     * @return string
     */
    public function getFormatedSize(int $precision = 2): string
    {
        $totalSize = $this->getTotalSize();
        
        $base = log($totalSize, self::BASE_CALCUL);
        $converted = round(pow(self::BASE_CALCUL, $base - floor($base)), $precision);

        return number_format($converted, $precision, ',', ' ') .' '. self::SUFFIXES[floor($base)];
    }
}

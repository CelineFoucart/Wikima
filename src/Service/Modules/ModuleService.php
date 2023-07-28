<?php

namespace App\Service\Modules;

class ModuleService
{
    /**
     * Available modules
     *
     * @var Module[]
     */
    private array $modules = [];

    public function __construct(private string $configFile, array $modules = [])
    {
        foreach ($modules as $module) {
            $status = isset($_ENV[$module['id']]) ? boolval($_ENV[$module['id']]) : false;
            $this->modules[$module['id']] = new Module($module['id'], $module['title'], $module['icon'], $module['description'], $status);
        }
    }

    /**
     * Get the value of modules
     *
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function handleSubmitData(array $data): bool
    {
        if (!file_exists($this->configFile)) {
            return false;
        }
        
        foreach (array_keys($this->modules) as $envVar) {
            $status = isset($data[$envVar]) ? 1 : 0;
            $this->persist($status, $envVar);
        }

        return true;
    }

    private function persist(int $status, string $key): void
    {
        $search = $key . '=' . (int)$this->modules[$key]->getStatus();

        if (preg_match('/'. $search . '/', file_get_contents($this->configFile))) {
            file_put_contents($this->configFile, str_replace(
                $search,
                $key . '=' . $status,
                file_get_contents($this->configFile)
            ));
        } else {
            file_put_contents($this->configFile, $key . '=' . $status . "\n", FILE_APPEND);
        }
    }
}

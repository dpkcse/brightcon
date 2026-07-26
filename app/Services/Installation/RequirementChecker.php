<?php

namespace App\Services\Installation;

class RequirementChecker
{
    public function check(): array
    {
        $items = [['name' => 'PHP', 'required' => '8.2+', 'detected' => PHP_VERSION, 'passed' => version_compare(PHP_VERSION, '8.2.0', '>='), 'blocking' => true]];
        foreach (['ctype', 'fileinfo', 'json', 'mbstring', 'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml'] as $extension) {
            $items[] = ['name' => $extension, 'required' => 'enabled', 'detected' => extension_loaded($extension) ? 'enabled' : 'missing', 'passed' => extension_loaded($extension), 'blocking' => true];
        }
        foreach (['curl', 'gd', 'zip'] as $extension) {
            $items[] = ['name' => $extension, 'required' => 'recommended', 'detected' => extension_loaded($extension) ? 'enabled' : 'missing', 'passed' => extension_loaded($extension), 'blocking' => false];
        }

        return $items;
    }

    public function passes(): bool
    {
        return collect($this->check())->where('blocking', true)->every('passed');
    }
}

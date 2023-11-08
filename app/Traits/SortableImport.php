<?php

namespace App\Traits;

trait SortableImport
{
    protected function sortImports($stub)
    {
        if (preg_match('/(?P<imports>(?:use [^;]+;$\n?)+)/m', $stub, $match))
        {
            $imports = explode("\n", trim($match['imports']));

            usort($imports, function ($a, $b) {
                return strlen($a) - strlen($b);
            });

            return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
        }

        return $stub;
    }
}

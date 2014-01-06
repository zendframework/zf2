<?php

namespace ZendTest\resources;

class LanguagesTest extends \PHPUnit_Framework_TestCase
{
    public function testLanguageResourcesHavePluralFormSet()
    {
        $langResPath = __DIR__ . '/../../../resources/languages';
        $ri = new \RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($langResPath)
        );
        while($ri->valid()) {
            if (!$ri->isDot() && strpos($ri->key(), '.php')!==false) {
                $result = include $ri->key();
                $this->assertArrayHasKey('', $result, 'Config sub-array not set in '. $ri->getSubPathName());
                $this->assertArrayHasKey('plural_forms', $result[''], 'Plural forms not set in ' . $ri->getSubPathName());
            }

            $ri->next();
        }
    }
}

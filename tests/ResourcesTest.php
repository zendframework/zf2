<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

class ResourcesTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->referenceKeys = $this->getTranslationKeys('en');
    }

    protected function getLanguagesDir()
    {
        return dirname(__DIR__) . '/resources/languages';
    }

    public function testEveryLanguageIsCompletelyCovered()
    {
        $output = array();
        foreach ($this->getAvailableLanguages() as $language) {
            $currentKeys = $this->getTranslationKeys($language);

            $missing = 0;
            foreach ($this->referenceKeys as $enKey => $value) {
                if (! isset($currentKeys[$enKey])) {
                    $missing++;
                }
            }

            $surplus = 0;
            foreach ($currentKeys as $currentKey => $value) {
                if (! isset($this->referenceKeys[$currentKey])) {
                    $surplus++;
                }
            }

            if ($missing or $surplus) {
                $output[] = sprintf('%-5s | %5s | %5s',
                    $language,
                    $missing ?: '-',
                    $surplus ?: '-'
                );
            }
        }

        if (! empty($output)) {
            array_unshift($output, 'LANG  | MISS  | PLUS ');
            $this->markTestIncomplete(implode(PHP_EOL, $output));
        }
    }

    public function getAvailableLanguages()
    {
        $languages = array();
        foreach (glob($this->getLanguagesDir() . '/*') as $dir) {
            $languages[] = basename($dir);
        }

        return $languages;
    }

    private function getTranslationKeys($language)
    {
        $glob = sprintf('%s/%s/*.php',
            $this->getLanguagesDir(),
            $language
        );
        $keys = array();
        foreach (glob($glob) as $file) {
            $keys = array_merge($keys, include $file);
        }
        ksort($keys);

        return $keys;
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Config\Writer;

class PhpArray extends AbstractWriter
{
    /**
     * processConfig(): defined by AbstractWriter.
     *
     * @param  array $config
     * @return string
     */
    public function processConfig(array $config)
    {
        $arrayString = "<?php\n"
                     . "return " . var_export($config, true) . ";\n";
        $arrayString = preg_replace("/=> \n + array/", '=> array', $arrayString);
        $arrayString = preg_replace_callback(
            '/^(  )+/m',
            function ($m) {
                return str_repeat($m[0], 2);
            },
            $arrayString
        );

        return $arrayString;
    }
}

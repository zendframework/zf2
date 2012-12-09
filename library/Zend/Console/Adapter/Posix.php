<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console\Adapter;

use Zend\Console\Charset;
use Zend\Console\Exception;
use Zend\Console\ColorInterface as Color;

/**
 * @todo Add GNU readline support
 * @category   Zend
 * @package    Zend_Console
 * @subpackage Adapter
 * @link http://en.wikipedia.org/wiki/ANSI_escape_code
 */
class Posix extends AbstractAdapter
{
    /**
     * Whether or not mbstring is enabled
     *
     * @var null|bool
     */
    protected static $hasMBString;

    /**
     * @var Charset\CharsetInterface
     */
    protected $charset;

    /**
     * Map of colors to ANSI codes
     *
     * @var array
     */
    protected static $ansiColorMap = array(
        'fg' => array(
            Color::NORMAL        => '22;39',
            Color::RESET         => '22;39',

            Color::BLACK         => '0;30',
            Color::RED           => '0;31',
            Color::GREEN         => '0;32',
            Color::YELLOW        => '0;33',
            Color::BLUE          => '0;34',
            Color::MAGENTA       => '0;35',
            Color::CYAN          => '0;36',
            Color::WHITE         => '0;37',

            Color::GRAY          => '1;30',
            Color::LIGHT_RED     => '1;31',
            Color::LIGHT_GREEN   => '1;32',
            Color::LIGHT_YELLOW  => '1;33',
            Color::LIGHT_BLUE    => '1;34',
            Color::LIGHT_MAGENTA => '1;35',
            Color::LIGHT_CYAN    => '1;36',
            Color::LIGHT_WHITE   => '1;37',
            
            Color::XTERM_000000  => '38;5;16',
            Color::XTERM_00005F  => '38;5;17',
            Color::XTERM_000087  => '38;5;18',
            Color::XTERM_0000AF  => '38;5;19',
            Color::XTERM_0000D7  => '38;5;20',
            Color::XTERM_0000FF  => '38;5;21',
            Color::XTERM_005F00  => '38;5;22',
            Color::XTERM_005F5F  => '38;5;23',
            Color::XTERM_005F87  => '38;5;24',
            Color::XTERM_005FAF  => '38;5;25',
            Color::XTERM_005FD7  => '38;5;26',
            Color::XTERM_005FFF  => '38;5;27',
            Color::XTERM_008700  => '38;5;28',
            Color::XTERM_00875F  => '38;5;29',
            Color::XTERM_008787  => '38;5;30',
            Color::XTERM_0087AF  => '38;5;31',
            Color::XTERM_0087D7  => '38;5;32',
            Color::XTERM_0087FF  => '38;5;33',
            Color::XTERM_00AF00  => '38;5;34',
            Color::XTERM_00AF5F  => '38;5;35',
            Color::XTERM_00AF87  => '38;5;36',
            Color::XTERM_00AFAF  => '38;5;37',
            Color::XTERM_00AFD7  => '38;5;38',
            Color::XTERM_00AFFF  => '38;5;39',
            Color::XTERM_00D700  => '38;5;40',
            Color::XTERM_00D75F  => '38;5;41',
            Color::XTERM_00D787  => '38;5;42',
            Color::XTERM_00D7AF  => '38;5;43',
            Color::XTERM_00D7D7  => '38;5;44',
            Color::XTERM_00D7FF  => '38;5;45',
            Color::XTERM_00FF00  => '38;5;46',
            Color::XTERM_00FF5F  => '38;5;47',
            Color::XTERM_00FF87  => '38;5;48',
            Color::XTERM_00FFAF  => '38;5;49',
            Color::XTERM_00FFD7  => '38;5;50',
            Color::XTERM_00FFFF  => '38;5;51',
            Color::XTERM_5F0000  => '38;5;52',
            Color::XTERM_5F005F  => '38;5;53',
            Color::XTERM_5F0087  => '38;5;54',
            Color::XTERM_5F00AF  => '38;5;55',
            Color::XTERM_5F00D7  => '38;5;56',
            Color::XTERM_5F00FF  => '38;5;57',
            Color::XTERM_5F5F00  => '38;5;58',
            Color::XTERM_5F5F5F  => '38;5;59',
            Color::XTERM_5F5F87  => '38;5;60',
            Color::XTERM_5F5FAF  => '38;5;61',
            Color::XTERM_5F5FD7  => '38;5;62',
            Color::XTERM_5F5FFF  => '38;5;63',
            Color::XTERM_5F8700  => '38;5;64',
            Color::XTERM_5F875F  => '38;5;65',
            Color::XTERM_5F8787  => '38;5;66',
            Color::XTERM_5F87AF  => '38;5;67',
            Color::XTERM_5F87D7  => '38;5;68',
            Color::XTERM_5F87FF  => '38;5;69',
            Color::XTERM_5FAF00  => '38;5;70',
            Color::XTERM_5FAF5F  => '38;5;71',
            Color::XTERM_5FAF87  => '38;5;72',
            Color::XTERM_5FAFAF  => '38;5;73',
            Color::XTERM_5FAFD7  => '38;5;74',
            Color::XTERM_5FAFFF  => '38;5;75',
            Color::XTERM_5FD700  => '38;5;76',
            Color::XTERM_5FD75F  => '38;5;77',
            Color::XTERM_5FD787  => '38;5;78',
            Color::XTERM_5FD7AF  => '38;5;79',
            Color::XTERM_5FD7D7  => '38;5;80',
            Color::XTERM_5FD7FF  => '38;5;81',
            Color::XTERM_5FFF00  => '38;5;82',
            Color::XTERM_5FFF5F  => '38;5;83',
            Color::XTERM_5FFF87  => '38;5;84',
            Color::XTERM_5FFFAF  => '38;5;85',
            Color::XTERM_5FFFD7  => '38;5;86',
            Color::XTERM_5FFFFF  => '38;5;87',
            Color::XTERM_870000  => '38;5;88',
            Color::XTERM_87005F  => '38;5;89',
            Color::XTERM_870087  => '38;5;90',
            Color::XTERM_8700AF  => '38;5;91',
            Color::XTERM_8700D7  => '38;5;92',
            Color::XTERM_8700FF  => '38;5;93',
            Color::XTERM_875F00  => '38;5;94',
            Color::XTERM_875F5F  => '38;5;95',
            Color::XTERM_875F87  => '38;5;96',
            Color::XTERM_875FAF  => '38;5;97',
            Color::XTERM_875FD7  => '38;5;98',
            Color::XTERM_875FFF  => '38;5;99',
            Color::XTERM_878700  => '38;5;100',
            Color::XTERM_87875F  => '38;5;101',
            Color::XTERM_878787  => '38;5;102',
            Color::XTERM_8787AF  => '38;5;103',
            Color::XTERM_8787D7  => '38;5;104',
            Color::XTERM_8787FF  => '38;5;105',
            Color::XTERM_87AF00  => '38;5;106',
            Color::XTERM_87AF5F  => '38;5;107',
            Color::XTERM_87AF87  => '38;5;108',
            Color::XTERM_87AFAF  => '38;5;109',
            Color::XTERM_87AFD7  => '38;5;110',
            Color::XTERM_87AFFF  => '38;5;111',
            Color::XTERM_87D700  => '38;5;112',
            Color::XTERM_87D75F  => '38;5;113',
            Color::XTERM_87D787  => '38;5;114',
            Color::XTERM_87D7AF  => '38;5;115',
            Color::XTERM_87D7D7  => '38;5;116',
            Color::XTERM_87D7FF  => '38;5;117',
            Color::XTERM_87FF00  => '38;5;118',
            Color::XTERM_87FF5F  => '38;5;119',
            Color::XTERM_87FF87  => '38;5;120',
            Color::XTERM_87FFAF  => '38;5;121',
            Color::XTERM_87FFD7  => '38;5;122',
            Color::XTERM_87FFFF  => '38;5;123',
            Color::XTERM_AF0000  => '38;5;124',
            Color::XTERM_AF005F  => '38;5;125',
            Color::XTERM_AF0087  => '38;5;126',
            Color::XTERM_AF00AF  => '38;5;127',
            Color::XTERM_AF00D7  => '38;5;128',
            Color::XTERM_AF00FF  => '38;5;129',
            Color::XTERM_AF5F00  => '38;5;130',
            Color::XTERM_AF5F5F  => '38;5;131',
            Color::XTERM_AF5F87  => '38;5;132',
            Color::XTERM_AF5FAF  => '38;5;133',
            Color::XTERM_AF5FD7  => '38;5;134',
            Color::XTERM_AF5FFF  => '38;5;135',
            Color::XTERM_AF8700  => '38;5;136',
            Color::XTERM_AF875F  => '38;5;137',
            Color::XTERM_AF8787  => '38;5;138',
            Color::XTERM_AF87AF  => '38;5;139',
            Color::XTERM_AF87D7  => '38;5;140',
            Color::XTERM_AF87FF  => '38;5;141',
            Color::XTERM_AFAF00  => '38;5;142',
            Color::XTERM_AFAF5F  => '38;5;143',
            Color::XTERM_AFAF87  => '38;5;144',
            Color::XTERM_AFAFAF  => '38;5;145',
            Color::XTERM_AFAFD7  => '38;5;146',
            Color::XTERM_AFAFFF  => '38;5;147',
            Color::XTERM_AFD700  => '38;5;148',
            Color::XTERM_AFD75F  => '38;5;149',
            Color::XTERM_AFD787  => '38;5;150',
            Color::XTERM_AFD7AF  => '38;5;151',
            Color::XTERM_AFD7D7  => '38;5;152',
            Color::XTERM_AFD7FF  => '38;5;153',
            Color::XTERM_AFFF00  => '38;5;154',
            Color::XTERM_AFFF5F  => '38;5;155',
            Color::XTERM_AFFF87  => '38;5;156',
            Color::XTERM_AFFFAF  => '38;5;157',
            Color::XTERM_AFFFD7  => '38;5;158',
            Color::XTERM_AFFFFF  => '38;5;159',
            Color::XTERM_D70000  => '38;5;160',
            Color::XTERM_D7005F  => '38;5;161',
            Color::XTERM_D70087  => '38;5;162',
            Color::XTERM_D700AF  => '38;5;163',
            Color::XTERM_D700D7  => '38;5;164',
            Color::XTERM_D700FF  => '38;5;165',
            Color::XTERM_D75F00  => '38;5;166',
            Color::XTERM_D75F5F  => '38;5;167',
            Color::XTERM_D75F87  => '38;5;168',
            Color::XTERM_D75FAF  => '38;5;169',
            Color::XTERM_D75FD7  => '38;5;170',
            Color::XTERM_D75FFF  => '38;5;171',
            Color::XTERM_D78700  => '38;5;172',
            Color::XTERM_D7875F  => '38;5;173',
            Color::XTERM_D78787  => '38;5;174',
            Color::XTERM_D787AF  => '38;5;175',
            Color::XTERM_D787D7  => '38;5;176',
            Color::XTERM_D787FF  => '38;5;177',
            Color::XTERM_D7AF00  => '38;5;178',
            Color::XTERM_D7AF5F  => '38;5;179',
            Color::XTERM_D7AF87  => '38;5;180',
            Color::XTERM_D7AFAF  => '38;5;181',
            Color::XTERM_D7AFD7  => '38;5;182',
            Color::XTERM_D7AFFF  => '38;5;183',
            Color::XTERM_D7D700  => '38;5;184',
            Color::XTERM_D7D75F  => '38;5;185',
            Color::XTERM_D7D787  => '38;5;186',
            Color::XTERM_D7D7AF  => '38;5;187',
            Color::XTERM_D7D7D7  => '38;5;188',
            Color::XTERM_D7D7FF  => '38;5;189',
            Color::XTERM_D7FF00  => '38;5;190',
            Color::XTERM_D7FF5F  => '38;5;191',
            Color::XTERM_D7FF87  => '38;5;192',
            Color::XTERM_D7FFAF  => '38;5;193',
            Color::XTERM_D7FFD7  => '38;5;194',
            Color::XTERM_D7FFFF  => '38;5;195',
            Color::XTERM_FF0000  => '38;5;196',
            Color::XTERM_FF005F  => '38;5;197',
            Color::XTERM_FF0087  => '38;5;198',
            Color::XTERM_FF00AF  => '38;5;199',
            Color::XTERM_FF00D7  => '38;5;200',
            Color::XTERM_FF00FF  => '38;5;201',
            Color::XTERM_FF5F00  => '38;5;202',
            Color::XTERM_FF5F5F  => '38;5;203',
            Color::XTERM_FF5F87  => '38;5;204',
            Color::XTERM_FF5FAF  => '38;5;205',
            Color::XTERM_FF5FD7  => '38;5;206',
            Color::XTERM_FF5FFF  => '38;5;207',
            Color::XTERM_FF8700  => '38;5;208',
            Color::XTERM_FF875F  => '38;5;209',
            Color::XTERM_FF8787  => '38;5;210',
            Color::XTERM_FF87AF  => '38;5;211',
            Color::XTERM_FF87D7  => '38;5;212',
            Color::XTERM_FF87FF  => '38;5;213',
            Color::XTERM_FFAF00  => '38;5;214',
            Color::XTERM_FFAF5F  => '38;5;215',
            Color::XTERM_FFAF87  => '38;5;216',
            Color::XTERM_FFAFAF  => '38;5;217',
            Color::XTERM_FFAFD7  => '38;5;218',
            Color::XTERM_FFAFFF  => '38;5;219',
            Color::XTERM_FFD700  => '38;5;220',
            Color::XTERM_FFD75F  => '38;5;221',
            Color::XTERM_FFD787  => '38;5;222',
            Color::XTERM_FFD7AF  => '38;5;223',
            Color::XTERM_FFD7D7  => '38;5;224',
            Color::XTERM_FFD7FF  => '38;5;225',
            Color::XTERM_FFFF00  => '38;5;226',
            Color::XTERM_FFFF5F  => '38;5;227',
            Color::XTERM_FFFF87  => '38;5;228',
            Color::XTERM_FFFFAF  => '38;5;229',
            Color::XTERM_FFFFD7  => '38;5;230',
            Color::XTERM_FFFFFF  => '38;5;231',
            Color::XTERM_080808  => '38;5;232',
            Color::XTERM_121212  => '38;5;233',
            Color::XTERM_1C1C1C  => '38;5;234',
            Color::XTERM_262626  => '38;5;235',
            Color::XTERM_303030  => '38;5;236',
            Color::XTERM_3A3A3A  => '38;5;237',
            Color::XTERM_444444  => '38;5;238',
            Color::XTERM_4E4E4E  => '38;5;239',
            Color::XTERM_585858  => '38;5;240',
            Color::XTERM_626262  => '38;5;241',
            Color::XTERM_6C6C6C  => '38;5;242',
            Color::XTERM_767676  => '38;5;243',
            Color::XTERM_808080  => '38;5;244',
            Color::XTERM_8A8A8A  => '38;5;245',
            Color::XTERM_949494  => '38;5;246',
            Color::XTERM_9E9E9E  => '38;5;247',
            Color::XTERM_A8A8A8  => '38;5;248',
            Color::XTERM_B2B2B2  => '38;5;249',
            Color::XTERM_BCBCBC  => '38;5;250',
            Color::XTERM_C6C6C6  => '38;5;251',
            Color::XTERM_D0D0D0  => '38;5;252',
            Color::XTERM_DADADA  => '38;5;253',
            Color::XTERM_E4E4E4  => '38;5;254',
            Color::XTERM_EEEEEE  => '38;5;255'
        ),
        'bg' => array(
            Color::NORMAL        => '0;49',
            Color::RESET         => '0;49',

            Color::BLACK         => '40',
            Color::RED           => '41',
            Color::GREEN         => '42',
            Color::YELLOW        => '43',
            Color::BLUE          => '44',
            Color::MAGENTA       => '45',
            Color::CYAN          => '46',
            Color::WHITE         => '47',

            Color::GRAY          => '40',
            Color::LIGHT_RED     => '41',
            Color::LIGHT_GREEN   => '42',
            Color::LIGHT_YELLOW  => '43',
            Color::LIGHT_BLUE    => '44',
            Color::LIGHT_MAGENTA => '45',
            Color::LIGHT_CYAN    => '46',
            Color::LIGHT_WHITE   => '47',
            
            Color::XTERM_000000  => '48;5;16',
            Color::XTERM_00005F  => '48;5;17',
            Color::XTERM_000087  => '48;5;18',
            Color::XTERM_0000AF  => '48;5;19',
            Color::XTERM_0000D7  => '48;5;20',
            Color::XTERM_0000FF  => '48;5;21',
            Color::XTERM_005F00  => '48;5;22',
            Color::XTERM_005F5F  => '48;5;23',
            Color::XTERM_005F87  => '48;5;24',
            Color::XTERM_005FAF  => '48;5;25',
            Color::XTERM_005FD7  => '48;5;26',
            Color::XTERM_005FFF  => '48;5;27',
            Color::XTERM_008700  => '48;5;28',
            Color::XTERM_00875F  => '48;5;29',
            Color::XTERM_008787  => '48;5;30',
            Color::XTERM_0087AF  => '48;5;31',
            Color::XTERM_0087D7  => '48;5;32',
            Color::XTERM_0087FF  => '48;5;33',
            Color::XTERM_00AF00  => '48;5;34',
            Color::XTERM_00AF5F  => '48;5;35',
            Color::XTERM_00AF87  => '48;5;36',
            Color::XTERM_00AFAF  => '48;5;37',
            Color::XTERM_00AFD7  => '48;5;38',
            Color::XTERM_00AFFF  => '48;5;39',
            Color::XTERM_00D700  => '48;5;40',
            Color::XTERM_00D75F  => '48;5;41',
            Color::XTERM_00D787  => '48;5;42',
            Color::XTERM_00D7AF  => '48;5;43',
            Color::XTERM_00D7D7  => '48;5;44',
            Color::XTERM_00D7FF  => '48;5;45',
            Color::XTERM_00FF00  => '48;5;46',
            Color::XTERM_00FF5F  => '48;5;47',
            Color::XTERM_00FF87  => '48;5;48',
            Color::XTERM_00FFAF  => '48;5;49',
            Color::XTERM_00FFD7  => '48;5;50',
            Color::XTERM_00FFFF  => '48;5;51',
            Color::XTERM_5F0000  => '48;5;52',
            Color::XTERM_5F005F  => '48;5;53',
            Color::XTERM_5F0087  => '48;5;54',
            Color::XTERM_5F00AF  => '48;5;55',
            Color::XTERM_5F00D7  => '48;5;56',
            Color::XTERM_5F00FF  => '48;5;57',
            Color::XTERM_5F5F00  => '48;5;58',
            Color::XTERM_5F5F5F  => '48;5;59',
            Color::XTERM_5F5F87  => '48;5;60',
            Color::XTERM_5F5FAF  => '48;5;61',
            Color::XTERM_5F5FD7  => '48;5;62',
            Color::XTERM_5F5FFF  => '48;5;63',
            Color::XTERM_5F8700  => '48;5;64',
            Color::XTERM_5F875F  => '48;5;65',
            Color::XTERM_5F8787  => '48;5;66',
            Color::XTERM_5F87AF  => '48;5;67',
            Color::XTERM_5F87D7  => '48;5;68',
            Color::XTERM_5F87FF  => '48;5;69',
            Color::XTERM_5FAF00  => '48;5;70',
            Color::XTERM_5FAF5F  => '48;5;71',
            Color::XTERM_5FAF87  => '48;5;72',
            Color::XTERM_5FAFAF  => '48;5;73',
            Color::XTERM_5FAFD7  => '48;5;74',
            Color::XTERM_5FAFFF  => '48;5;75',
            Color::XTERM_5FD700  => '48;5;76',
            Color::XTERM_5FD75F  => '48;5;77',
            Color::XTERM_5FD787  => '48;5;78',
            Color::XTERM_5FD7AF  => '48;5;79',
            Color::XTERM_5FD7D7  => '48;5;80',
            Color::XTERM_5FD7FF  => '48;5;81',
            Color::XTERM_5FFF00  => '48;5;82',
            Color::XTERM_5FFF5F  => '48;5;83',
            Color::XTERM_5FFF87  => '48;5;84',
            Color::XTERM_5FFFAF  => '48;5;85',
            Color::XTERM_5FFFD7  => '48;5;86',
            Color::XTERM_5FFFFF  => '48;5;87',
            Color::XTERM_870000  => '48;5;88',
            Color::XTERM_87005F  => '48;5;89',
            Color::XTERM_870087  => '48;5;90',
            Color::XTERM_8700AF  => '48;5;91',
            Color::XTERM_8700D7  => '48;5;92',
            Color::XTERM_8700FF  => '48;5;93',
            Color::XTERM_875F00  => '48;5;94',
            Color::XTERM_875F5F  => '48;5;95',
            Color::XTERM_875F87  => '48;5;96',
            Color::XTERM_875FAF  => '48;5;97',
            Color::XTERM_875FD7  => '48;5;98',
            Color::XTERM_875FFF  => '48;5;99',
            Color::XTERM_878700  => '48;5;100',
            Color::XTERM_87875F  => '48;5;101',
            Color::XTERM_878787  => '48;5;102',
            Color::XTERM_8787AF  => '48;5;103',
            Color::XTERM_8787D7  => '48;5;104',
            Color::XTERM_8787FF  => '48;5;105',
            Color::XTERM_87AF00  => '48;5;106',
            Color::XTERM_87AF5F  => '48;5;107',
            Color::XTERM_87AF87  => '48;5;108',
            Color::XTERM_87AFAF  => '48;5;109',
            Color::XTERM_87AFD7  => '48;5;110',
            Color::XTERM_87AFFF  => '48;5;111',
            Color::XTERM_87D700  => '48;5;112',
            Color::XTERM_87D75F  => '48;5;113',
            Color::XTERM_87D787  => '48;5;114',
            Color::XTERM_87D7AF  => '48;5;115',
            Color::XTERM_87D7D7  => '48;5;116',
            Color::XTERM_87D7FF  => '48;5;117',
            Color::XTERM_87FF00  => '48;5;118',
            Color::XTERM_87FF5F  => '48;5;119',
            Color::XTERM_87FF87  => '48;5;120',
            Color::XTERM_87FFAF  => '48;5;121',
            Color::XTERM_87FFD7  => '48;5;122',
            Color::XTERM_87FFFF  => '48;5;123',
            Color::XTERM_AF0000  => '48;5;124',
            Color::XTERM_AF005F  => '48;5;125',
            Color::XTERM_AF0087  => '48;5;126',
            Color::XTERM_AF00AF  => '48;5;127',
            Color::XTERM_AF00D7  => '48;5;128',
            Color::XTERM_AF00FF  => '48;5;129',
            Color::XTERM_AF5F00  => '48;5;130',
            Color::XTERM_AF5F5F  => '48;5;131',
            Color::XTERM_AF5F87  => '48;5;132',
            Color::XTERM_AF5FAF  => '48;5;133',
            Color::XTERM_AF5FD7  => '48;5;134',
            Color::XTERM_AF5FFF  => '48;5;135',
            Color::XTERM_AF8700  => '48;5;136',
            Color::XTERM_AF875F  => '48;5;137',
            Color::XTERM_AF8787  => '48;5;138',
            Color::XTERM_AF87AF  => '48;5;139',
            Color::XTERM_AF87D7  => '48;5;140',
            Color::XTERM_AF87FF  => '48;5;141',
            Color::XTERM_AFAF00  => '48;5;142',
            Color::XTERM_AFAF5F  => '48;5;143',
            Color::XTERM_AFAF87  => '48;5;144',
            Color::XTERM_AFAFAF  => '48;5;145',
            Color::XTERM_AFAFD7  => '48;5;146',
            Color::XTERM_AFAFFF  => '48;5;147',
            Color::XTERM_AFD700  => '48;5;148',
            Color::XTERM_AFD75F  => '48;5;149',
            Color::XTERM_AFD787  => '48;5;150',
            Color::XTERM_AFD7AF  => '48;5;151',
            Color::XTERM_AFD7D7  => '48;5;152',
            Color::XTERM_AFD7FF  => '48;5;153',
            Color::XTERM_AFFF00  => '48;5;154',
            Color::XTERM_AFFF5F  => '48;5;155',
            Color::XTERM_AFFF87  => '48;5;156',
            Color::XTERM_AFFFAF  => '48;5;157',
            Color::XTERM_AFFFD7  => '48;5;158',
            Color::XTERM_AFFFFF  => '48;5;159',
            Color::XTERM_D70000  => '48;5;160',
            Color::XTERM_D7005F  => '48;5;161',
            Color::XTERM_D70087  => '48;5;162',
            Color::XTERM_D700AF  => '48;5;163',
            Color::XTERM_D700D7  => '48;5;164',
            Color::XTERM_D700FF  => '48;5;165',
            Color::XTERM_D75F00  => '48;5;166',
            Color::XTERM_D75F5F  => '48;5;167',
            Color::XTERM_D75F87  => '48;5;168',
            Color::XTERM_D75FAF  => '48;5;169',
            Color::XTERM_D75FD7  => '48;5;170',
            Color::XTERM_D75FFF  => '48;5;171',
            Color::XTERM_D78700  => '48;5;172',
            Color::XTERM_D7875F  => '48;5;173',
            Color::XTERM_D78787  => '48;5;174',
            Color::XTERM_D787AF  => '48;5;175',
            Color::XTERM_D787D7  => '48;5;176',
            Color::XTERM_D787FF  => '48;5;177',
            Color::XTERM_D7AF00  => '48;5;178',
            Color::XTERM_D7AF5F  => '48;5;179',
            Color::XTERM_D7AF87  => '48;5;180',
            Color::XTERM_D7AFAF  => '48;5;181',
            Color::XTERM_D7AFD7  => '48;5;182',
            Color::XTERM_D7AFFF  => '48;5;183',
            Color::XTERM_D7D700  => '48;5;184',
            Color::XTERM_D7D75F  => '48;5;185',
            Color::XTERM_D7D787  => '48;5;186',
            Color::XTERM_D7D7AF  => '48;5;187',
            Color::XTERM_D7D7D7  => '48;5;188',
            Color::XTERM_D7D7FF  => '48;5;189',
            Color::XTERM_D7FF00  => '48;5;190',
            Color::XTERM_D7FF5F  => '48;5;191',
            Color::XTERM_D7FF87  => '48;5;192',
            Color::XTERM_D7FFAF  => '48;5;193',
            Color::XTERM_D7FFD7  => '48;5;194',
            Color::XTERM_D7FFFF  => '48;5;195',
            Color::XTERM_FF0000  => '48;5;196',
            Color::XTERM_FF005F  => '48;5;197',
            Color::XTERM_FF0087  => '48;5;198',
            Color::XTERM_FF00AF  => '48;5;199',
            Color::XTERM_FF00D7  => '48;5;200',
            Color::XTERM_FF00FF  => '48;5;201',
            Color::XTERM_FF5F00  => '48;5;202',
            Color::XTERM_FF5F5F  => '48;5;203',
            Color::XTERM_FF5F87  => '48;5;204',
            Color::XTERM_FF5FAF  => '48;5;205',
            Color::XTERM_FF5FD7  => '48;5;206',
            Color::XTERM_FF5FFF  => '48;5;207',
            Color::XTERM_FF8700  => '48;5;208',
            Color::XTERM_FF875F  => '48;5;209',
            Color::XTERM_FF8787  => '48;5;210',
            Color::XTERM_FF87AF  => '48;5;211',
            Color::XTERM_FF87D7  => '48;5;212',
            Color::XTERM_FF87FF  => '48;5;213',
            Color::XTERM_FFAF00  => '48;5;214',
            Color::XTERM_FFAF5F  => '48;5;215',
            Color::XTERM_FFAF87  => '48;5;216',
            Color::XTERM_FFAFAF  => '48;5;217',
            Color::XTERM_FFAFD7  => '48;5;218',
            Color::XTERM_FFAFFF  => '48;5;219',
            Color::XTERM_FFD700  => '48;5;220',
            Color::XTERM_FFD75F  => '48;5;221',
            Color::XTERM_FFD787  => '48;5;222',
            Color::XTERM_FFD7AF  => '48;5;223',
            Color::XTERM_FFD7D7  => '48;5;224',
            Color::XTERM_FFD7FF  => '48;5;225',
            Color::XTERM_FFFF00  => '48;5;226',
            Color::XTERM_FFFF5F  => '48;5;227',
            Color::XTERM_FFFF87  => '48;5;228',
            Color::XTERM_FFFFAF  => '48;5;229',
            Color::XTERM_FFFFD7  => '48;5;230',
            Color::XTERM_FFFFFF  => '48;5;231',
            Color::XTERM_080808  => '48;5;232',
            Color::XTERM_121212  => '48;5;233',
            Color::XTERM_1C1C1C  => '48;5;234',
            Color::XTERM_262626  => '48;5;235',
            Color::XTERM_303030  => '48;5;236',
            Color::XTERM_3A3A3A  => '48;5;237',
            Color::XTERM_444444  => '48;5;238',
            Color::XTERM_4E4E4E  => '48;5;239',
            Color::XTERM_585858  => '48;5;240',
            Color::XTERM_626262  => '48;5;241',
            Color::XTERM_6C6C6C  => '48;5;242',
            Color::XTERM_767676  => '48;5;243',
            Color::XTERM_808080  => '48;5;244',
            Color::XTERM_8A8A8A  => '48;5;245',
            Color::XTERM_949494  => '48;5;246',
            Color::XTERM_9E9E9E  => '48;5;247',
            Color::XTERM_A8A8A8  => '48;5;248',
            Color::XTERM_B2B2B2  => '48;5;249',
            Color::XTERM_BCBCBC  => '48;5;250',
            Color::XTERM_C6C6C6  => '48;5;251',
            Color::XTERM_D0D0D0  => '48;5;252',
            Color::XTERM_DADADA  => '48;5;253',
            Color::XTERM_E4E4E4  => '48;5;254',
            Color::XTERM_EEEEEE  => '48;5;255'
        ),
    );

    /**
     * Last fetched TTY mode
     *
     * @var string|null
     */
    protected $lastTTYMode = null;

    /**
     * Determine and return current console width.
     *
     * @return int
     */
    public function getWidth()
    {
        static $width;
        if ($width > 0) {
            return $width;
        }

        /**
         * Try to read env variable
         */
        if (($result = getenv('COLUMNS')) !== false) {
            return $width = (int) $result;
        }

        /**
         * Try to read console size from "tput" command
         */
        $result = exec('tput cols', $output, $return);
        if (!$return && is_numeric($result)) {
            return $width = (int) $result;
        }

        return $width = parent::getWidth();
    }

    /**
     * Determine and return current console height.
     *
     * @return false|int
     */
    public function getHeight()
    {
        static $height;
        if ($height > 0) {
            return $height;
        }

        // Try to read env variable
        if (($result = getenv('LINES')) !== false) {
            return $height = (int) $result;
        }

        // Try to read console size from "tput" command
        $result = exec('tput lines', $output, $return);
        if (!$return && is_numeric($result)) {
            return $height = (int) $result;
        }

        return $height = parent::getHeight();
    }

    /**
     * Run a mode command and store results
     *
     * @return void
     */
    protected function runModeCommand()
    {
        exec('mode', $output, $return);
        if ($return || !count($output)) {
            $this->modeResult = '';
        } else {
            $this->modeResult = trim(implode('', $output));
        }
    }

    /**
     * Check if console is UTF-8 compatible
     *
     * @return bool
     */
    public function isUtf8()
    {
        // Try to retrieve it from LANG env variable
        if (($lang = getenv('LANG')) !== false) {
            return stristr($lang, 'utf-8') || stristr($lang, 'utf8');
        }

        return false;
    }

    /**
     * Show console cursor
     */
    public function showCursor()
    {
        echo "\x1b[?25h";
    }

    /**
     * Hide console cursor
     */
    public function hideCursor()
    {
        echo "\x1b[?25l";
    }

    /**
     * Set cursor position
     * @param int $x
     * @param int $y
     */
    public function setPos($x, $y)
    {
        echo "\x1b[" . $y . ';' . $x . 'f';
    }

    /**
     * Prepare a string that will be rendered in color.
     *
     * @param  string   $string
     * @param  int      $color
     * @param  null|int $bgColor
     * @throws Exception\BadMethodCallException
     * @return string
     */
    public function colorize($string, $color = null, $bgColor = null)
    {
        // Retrieve ansi color codes
        if ($color !== null) {
            if (!isset(static::$ansiColorMap['fg'][$color])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $color
                ));
            }
            $color = static::$ansiColorMap['fg'][$color];
        }

        if ($bgColor !== null) {
            if (!isset(static::$ansiColorMap['bg'][$bgColor])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $bgColor
                ));
            }
            $bgColor = static::$ansiColorMap['bg'][$bgColor];
        }

        return ($color   !== null ? "\x1b[" . $color   . 'm' : '')
            . ($bgColor !== null ? "\x1b[" . $bgColor . 'm' : '')
            . $string
            . "\x1b[22;39m\x1b[0;49m";
    }

    /**
     * Change current drawing color.
     *
     * @param int $color
     * @throws Exception\BadMethodCallException
     */
    public function setColor($color)
    {
        // Retrieve ansi color code
        if ($color !== null) {
            if (!isset(static::$ansiColorMap['fg'][$color])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $color
                ));
            }
            $color = static::$ansiColorMap['fg'][$color];
        }

        echo "\x1b[" . $color . 'm';
    }

    /**
     * Change current drawing background color
     *
     * @param int $bgColor
     * @throws Exception\BadMethodCallException
     */
    public function setBgColor($bgColor)
    {
        // Retrieve ansi color code
        if ($bgColor !== null) {
            if (!isset(static::$ansiColorMap['bg'][$bgColor])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $bgColor
                ));
            }

            $bgColor = static::$ansiColorMap['bg'][$bgColor];
        }

        echo "\x1b[" . ($bgColor) . 'm';
    }

    /**
     * Reset color to console default.
     */
    public function resetColor()
    {
        echo "\x1b[0;49m";  // reset bg color
        echo "\x1b[22;39m"; // reset fg bold, bright and faint
        echo "\x1b[25;39m"; // reset fg blink
        echo "\x1b[24;39m"; // reset fg underline
    }

    /**
     * Return current console window title.
     *
     * @return string
     */
    public function getTitle()
    {
    }

    /**
     * Set Console charset to use.
     *
     * @param Charset\CharsetInterface $charset
     */
    public function setCharset(Charset\CharsetInterface $charset)
    {
        $this->charset = $charset;
    }

    /**
     * Get charset currently in use by this adapter.
     *
     * @return Charset\CharsetInterface $charset
     */
    public function getCharset()
    {
        if ($this->charset === null) {
            $this->charset = $this->getDefaultCharset();
        }

        return $this->charset;
    }

    /**
     * @return Charset\CharsetInterface
     */
    public function getDefaultCharset()
    {
        if ($this->isUtf8()) {
            return new Charset\Utf8;
        }
        return new Charset\DECSG();
    }

    /**
     * Read a single character from the console input
     *
     * @param  string|null $mask   A list of allowed chars
     * @return string
     */
    public function readChar($mask = null)
    {
        $this->setTTYMode('-icanon -echo');

        $stream = fopen('php://stdin', 'rb');
        do {
            $char = fgetc($stream);
        } while (strlen($char) !== 1 || ($mask !== null && stristr($mask, $char) === false));
        fclose($stream);

        $this->restoreTTYMode();
        return $char;
    }

    /**
     * Reset color to console default.
     */
    public function clear()
    {
        echo "\x1b[2J";      // reset bg color
        $this->setPos(1, 1); // reset cursor position
    }

    /**
     * Restore TTY (Console) mode to previous value.
     *
     * @return void
     */
    protected function restoreTTYMode()
    {
        if ($this->lastTTYMode === null) {
            return;
        }

        shell_exec('stty ' . escapeshellarg($this->lastTTYMode));
    }

    /**
     * Change TTY (Console) mode
     *
     * @link  http://en.wikipedia.org/wiki/Stty
     * @param $mode
     */
    protected function setTTYMode($mode)
    {
        // Store last mode
        $this->lastTTYMode = trim(`stty -g`);

        // Set new mode
        shell_exec('stty '.escapeshellcmd($mode));
    }
}

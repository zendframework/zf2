<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Validator;

use Locale;
use Zend\I18n\Validator\PhoneNumber;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhoneNumber
     */
    protected $validator;

    /**
     * @var array
     */
    protected $phone = [
        'AC' => [
            'code'     => '247',
            'patterns' => [
                'example' => [
                    'fixed'     => '6889',
                    'emergency' => '911',
                ],
            ],
        ],
        'AD' => [
            'code'     => '376',
            'patterns' => [
                'example' => [
                    'fixed'     => '712345',
                    'mobile'    => '312345',
                    'tollfree'  => '18001234',
                    'premium'   => '912345',
                    'emergency' => '112',
                ],
            ],
        ],
        'AE' => [
            'code'     => '971',
            'patterns' => [
                'example' => [
                    'fixed'     => '22345678',
                    'mobile'    => '501234567',
                    'tollfree'  => '800123456',
                    'premium'   => '900234567',
                    'shared'    => '700012345',
                    'uan'       => '600212345',
                    'emergency' => '112',
                ],
            ],
        ],
        'AF' => [
            'code'     => '93',
            'patterns' => [
                'example' => [
                    'fixed'     => '234567890',
                    'mobile'    => '701234567',
                    'emergency' => '119',
                ],
            ],
        ],
        'AG' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '2684601234',
                    'mobile'    => '2684641234',
                    'pager'     => '2684061234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'voip'      => '2684801234',
                    'emergency' => '911',
                ],
            ],
        ],
        'AI' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '2644612345',
                    'mobile'    => '2642351234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'AL' => [
            'code'     => '355',
            'patterns' => [
                'example' => [
                    'fixed'     => '22345678',
                    'mobile'    => '661234567',
                    'tollfree'  => '8001234',
                    'premium'   => '900123',
                    'shared'    => '808123',
                    'personal'  => '70012345',
                    'emergency' => '129',
                ],
            ],
        ],
        'AM' => [
            'code'     => '374',
            'patterns' => [
                'example' => [
                    'fixed'     => '10123456',
                    'mobile'    => '77123456',
                    'tollfree'  => '80012345',
                    'premium'   => '90012345',
                    'shared'    => '80112345',
                    'voip'      => '60271234',
                    'shortcode' => '8711',
                    'emergency' => '102',
                ],
            ],
        ],
        'AO' => [
            'code'     => '244',
            'patterns' => [
                'example' => [
                    'fixed'     => '222123456',
                    'mobile'    => '923123456',
                    'emergency' => '113',
                ],
            ],
        ],
        'AR' => [
            'code'     => '54',
            'patterns' => [
                'example' => [
                    'fixed'     => '1123456789',
                    'mobile'    => '91123456789',
                    'tollfree'  => '8001234567',
                    'premium'   => '6001234567',
                    'uan'       => '8101234567',
                    'shortcode' => '121',
                    'emergency' => '101',
                ],
            ],
        ],
        'AS' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '6846221234',
                    'mobile'    => '6847331234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'AT' => [
            'code'     => '43',
            'patterns' => [
                'example' => [
                    'fixed'     => '1234567890',
                    'mobile'    => '644123456',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'shared'    => '810123456',
                    'voip'      => '780123456',
                    'uan'       => '50123',
                    'emergency' => '112',
                ],
            ],
        ],
        'AU' => [
            'code'     => '61',
            'patterns' => [
                'example' => [
                    'fixed'     => '212345678',
                    'mobile'    => '412345678',
                    'pager'     => '1612345',
                    'tollfree'  => '1800123456',
                    'premium'   => '1900123456',
                    'shared'    => '1300123456',
                    'personal'  => '500123456',
                    'voip'      => '550123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'AW' => [
            'code'     => '297',
            'patterns' => [
                'example' => [
                    'fixed'     => '5212345',
                    'mobile'    => '5601234',
                    'tollfree'  => '8001234',
                    'premium'   => '9001234',
                    'voip'      => '5011234',
                    'emergency' => '911',
                ],
            ],
        ],
        'AX' => [
            'code'     => '358',
            'patterns' => [
                'example' => [
                    'fixed'     => '1812345678',
                    'mobile'    => '412345678',
                    'tollfree'  => '8001234567',
                    'premium'   => '600123456',
                    'uan'       => '10112345',
                    'emergency' => '112',
                ],
            ],
        ],
        'AZ' => [
            'code'     => '994',
            'patterns' => [
                'example' => [
                    'fixed'     => '123123456',
                    'mobile'    => '401234567',
                    'tollfree'  => '881234567',
                    'premium'   => '900200123',
                    'emergency' => '101',
                ],
            ],
        ],
        'BA' => [
            'code'     => '387',
            'patterns' => [
                'example' => [
                    'fixed'     => '30123456',
                    'mobile'    => '61123456',
                    'tollfree'  => '80123456',
                    'premium'   => '90123456',
                    'shared'    => '82123456',
                    'uan'       => '70223456',
                    'emergency' => '122',
                ],
            ],
        ],
        'BB' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '2462345678',
                    'mobile'    => '2462501234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '211',
                ],
            ],
        ],
        'BD' => [
            'code'     => '880',
            'patterns' => [
                'example' => [
                    'fixed'     => '27111234',
                    'mobile'    => '1812345678',
                    'tollfree'  => '8001234567',
                    'voip'      => '9604123456',
                    'shortcode' => '103',
                    'emergency' => '999',
                ],
            ],
        ],
        'BE' => [
            'code'     => '32',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'mobile'    => '470123456',
                    'tollfree'  => '80012345',
                    'premium'   => '90123456',
                    'uan'       => '78123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'BF' => [
            'code'     => '226',
            'patterns' => [
                'example' => [
                    'fixed'     => '20491234',
                    'mobile'    => '70123456',
                    'emergency' => '17',
                ],
            ],
        ],
        'BG' => [
            'code'     => '359',
            'patterns' => [
                'example' => [
                    'fixed'     => '2123456',
                    'mobile'    => '48123456',
                    'tollfree'  => '80012345',
                    'premium'   => '90123456',
                    'personal'  => '70012345',
                    'emergency' => '112',
                ],
            ],
        ],
        'BH' => [
            'code'     => '973',
            'patterns' => [
                'example' => [
                    'fixed'     => '17001234',
                    'mobile'    => '36001234',
                    'tollfree'  => '80123456',
                    'premium'   => '90123456',
                    'shared'    => '84123456',
                    'emergency' => '999',
                ],
            ],
        ],
        'BI' => [
            'code'     => '257',
            'patterns' => [
                'example' => [
                    'fixed'     => '22201234',
                    'mobile'    => '79561234',
                    'emergency' => '117',
                ],
            ],
        ],
        'BJ' => [
            'code'     => '229',
            'patterns' => [
                'example' => [
                    'fixed'     => '20211234',
                    'mobile'    => '90011234',
                    'tollfree'  => '7312',
                    'voip'      => '85751234',
                    'uan'       => '81123456',
                    'emergency' => '117',
                ],
            ],
        ],
        'BL' => [
            'code'     => '590',
            'patterns' => [
                'example' => [
                    'fixed'     => '590271234',
                    'mobile'    => '690221234',
                    'emergency' => '18',
                ],
            ],
        ],
        'BM' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '4412345678',
                    'mobile'    => '4413701234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'BN' => [
            'code'     => '673',
            'patterns' => [
                'example' => [
                    'fixed'     => '2345678',
                    'mobile'    => '7123456',
                    'emergency' => '991',
                ],
            ],
        ],
        'BO' => [
            'code'     => '591',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123456',
                    'mobile'    => '71234567',
                    'emergency' => '110',
                ],
            ],
        ],
        'BQ' => [
            'code'     => '599',
            'patterns' => [
                'example' => [
                    'fixed'     => '7151234',
                    'mobile'    => '3181234',
                    'emergency' => '112',
                ],
            ],
        ],
        'BR' => [
            'code'     => '55',
            'patterns' => [
                'example' => [
                    'fixed'     => '1123456789',
                    'mobile'    => '1161234567',
                    'tollfree'  => '800123456',
                    'premium'   => '300123456',
                    'shared'    => '40041234',
                    'emergency' => '190',
                ],
            ],
        ],
        'BS' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '2423456789',
                    'mobile'    => '2423591234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'BT' => [
            'code'     => '975',
            'patterns' => [
                'example' => [
                    'fixed'     => '2345678',
                    'mobile'    => '17123456',
                    'emergency' => '113',
                ],
            ],
        ],
        'BW' => [
            'code'     => '267',
            'patterns' => [
                'example' => [
                    'fixed'     => '2401234',
                    'mobile'    => '71123456',
                    'premium'   => '9012345',
                    'voip'      => '79101234',
                    'emergency' => '999',
                ],
            ],
        ],
        'BY' => [
            'code'     => '375',
            'patterns' => [
                'example' => [
                    'fixed'     => '152450911',
                    'mobile'    => '294911911',
                    'tollfree'  => '8011234567',
                    'premium'   => '9021234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'BZ' => [
            'code'     => '501',
            'patterns' => [
                'example' => [
                    'fixed'     => '2221234',
                    'mobile'    => '6221234',
                    'tollfree'  => '08001234123',
                    'emergency' => '911',
                ],
            ],
        ],
        'CA' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '2042345678',
                    'mobile'    => '2042345678',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'CC' => [
            'code'     => '61',
            'patterns' => [
                'example' => [
                    'fixed'     => '891621234',
                    'mobile'    => '412345678',
                    'tollfree'  => '1800123456',
                    'premium'   => '1900123456',
                    'personal'  => '500123456',
                    'voip'      => '550123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'CD' => [
            'code'     => '243',
            'patterns' => [
                'example' => [
                    'fixed'  => '1234567',
                    'mobile' => '991234567',
                ],
            ],
        ],
        'CF' => [
            'code'     => '236',
            'patterns' => [
                'example' => [
                    'fixed'   => '21612345',
                    'mobile'  => '70012345',
                    'premium' => '87761234',
                ],
            ],
        ],
        'CG' => [
            'code'     => '242',
            'patterns' => [
                'example' => [
                    'fixed'    => '222123456',
                    'mobile'   => '061234567',
                    'tollfree' => '800123456',
                ],
            ],
        ],
        'CH' => [
            'code'     => '41',
            'patterns' => [
                'example' => [
                    'fixed'     => '212345678',
                    'mobile'    => '741234567',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'shared'    => '840123456',
                    'personal'  => '878123456',
                    'voicemail' => '860123456789',
                    'emergency' => '112',
                ],
            ],
        ],
        'CI' => [
            'code'     => '225',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '01234567',
                    'emergency' => '110',
                ],
            ],
        ],
        'CK' => [
            'code'     => '682',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234',
                    'mobile'    => '71234',
                    'emergency' => '998',
                ],
            ],
        ],
        'CL' => [
            'code'     => '56',
            'patterns' => [
                'example' => [
                    'fixed'     => '221234567',
                    'mobile'    => '961234567',
                    'tollfree'  => '800123456',
                    'shared'    => '6001234567',
                    'voip'      => '441234567',
                    'emergency' => '133',
                ],
            ],
        ],
        'CM' => [
            'code'     => '237',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123456',
                    'mobile'    => '71234567',
                    'tollfree'  => '80012345',
                    'premium'   => '88012345',
                    'emergency' => '113',
                ],
            ],
        ],
        'CN' => [
            'code'     => '86',
            'patterns' => [
                'example' => [
                    'fixed'     => '1012345678',
                    'mobile'    => '13123456789',
                    'tollfree'  => '8001234567',
                    'premium'   => '16812345',
                    'shared'    => '4001234567',
                    'emergency' => '119',
                ],
            ],
        ],
        'CO' => [
            'code'     => '57',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'mobile'    => '3211234567',
                    'tollfree'  => '18001234567',
                    'premium'   => '19001234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'CR' => [
            'code'     => '506',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123456',
                    'mobile'    => '83123456',
                    'tollfree'  => '8001234567',
                    'premium'   => '9001234567',
                    'voip'      => '40001234',
                    'shortcode' => '1022',
                    'emergency' => '911',
                ],
            ],
        ],
        'CU' => [
            'code'     => '53',
            'patterns' => [
                'example' => [
                    'fixed'     => '71234567',
                    'mobile'    => '51234567',
                    'shortcode' => '140',
                    'emergency' => '106',
                ],
            ],
        ],
        'CV' => [
            'code'     => '238',
            'patterns' => [
                'example' => [
                    'fixed'     => '2211234',
                    'mobile'    => '9911234',
                    'emergency' => '132',
                ],
            ],
        ],
        'CW' => [
            'code'     => '599',
            'patterns' => [
                'example' => [
                    'fixed'     => '94151234',
                    'mobile'    => '95181234',
                    'pager'     => '95581234',
                    'shared'    => '1011234',
                    'emergency' => '112',
                ],
            ],
        ],
        'CX' => [
            'code'     => '61',
            'patterns' => [
                'example' => [
                    'fixed'     => '891641234',
                    'mobile'    => '412345678',
                    'tollfree'  => '1800123456',
                    'premium'   => '1900123456',
                    'personal'  => '500123456',
                    'voip'      => '550123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'CY' => [
            'code'     => '357',
            'patterns' => [
                'example' => [
                    'fixed'     => '22345678',
                    'mobile'    => '96123456',
                    'tollfree'  => '80001234',
                    'premium'   => '90012345',
                    'shared'    => '80112345',
                    'personal'  => '70012345',
                    'uan'       => '77123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'CZ' => [
            'code'     => '420',
            'patterns' => [
                'example' => [
                    'fixed'     => '212345678',
                    'mobile'    => '601123456',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'shared'    => '811234567',
                    'personal'  => '700123456',
                    'voip'      => '910123456',
                    'uan'       => '972123456',
                    'voicemail' => '93123456789',
                    'shortcode' => '116123',
                    'emergency' => '112',
                ],
            ],
        ],
        'DE' => [
            'code'     => '49',
            'patterns' => [
                'example' => [
                    'fixed'     => '30123456',
                    'mobile'    => '15123456789',
                    'pager'     => '16412345',
                    'tollfree'  => '8001234567890',
                    'premium'   => '9001234567',
                    'shared'    => '18012345',
                    'personal'  => '70012345678',
                    'uan'       => '18500123456',
                    'voicemail' => '177991234567',
                    'shortcode' => '115',
                    'emergency' => '112',
                ],
            ],
        ],
        'DJ' => [
            'code'     => '253',
            'patterns' => [
                'example' => [
                    'fixed'     => '21360003',
                    'mobile'    => '77831001',
                    'emergency' => '17',
                ],
            ],
        ],
        'DK' => [
            'code'     => '45',
            'patterns' => [
                'example' => [
                    'fixed'     => '32123456',
                    'mobile'    => '20123456',
                    'tollfree'  => '80123456',
                    'premium'   => '90123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'DM' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '7674201234',
                    'mobile'    => '7672251234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '999',
                ],
            ],
        ],
        'DO' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '8092345678',
                    'mobile'    => '8092345678',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'DZ' => [
            'code'     => '213',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'mobile'    => '551234567',
                    'tollfree'  => '800123456',
                    'premium'   => '808123456',
                    'shared'    => '801123456',
                    'voip'      => '983123456',
                    'emergency' => '17',
                ],
            ],
        ],
        'EC' => [
            'code'     => '593',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123456',
                    'mobile'    => '991234567',
                    'tollfree'  => '18001234567',
                    'voip'      => '28901234',
                    'emergency' => '911',
                ],
            ],
        ],
        'EE' => [
            'code'     => '372',
            'patterns' => [
                'example' => [
                    'fixed'     => '3212345',
                    'mobile'    => '51234567',
                    'tollfree'  => '80012345',
                    'premium'   => '9001234',
                    'personal'  => '70012345',
                    'uan'       => '12123',
                    'shortcode' => '116',
                    'emergency' => '112',
                ],
            ],
        ],
        'EG' => [
            'code'     => '20',
            'patterns' => [
                'example' => [
                    'fixed'     => '234567890',
                    'mobile'    => '1001234567',
                    'tollfree'  => '8001234567',
                    'premium'   => '9001234567',
                    'emergency' => '122',
                ],
            ],
        ],
        'EH' => [
            'code'     => '212',
            'patterns' => [
                'example' => [
                    'fixed'     => '528812345',
                    'mobile'    => '650123456',
                    'tollfree'  => '801234567',
                    'premium'   => '891234567',
                    'emergency' => '15',
                ],
            ],
        ],
        'ER' => [
            'code'     => '291',
            'patterns' => [
                'example' => [
                    'fixed'  => '8370362',
                    'mobile' => '7123456',
                ],
            ],
        ],
        'ES' => [
            'code'     => '34',
            'patterns' => [
                'example' => [
                    'fixed'     => '810123456',
                    'mobile'    => '612345678',
                    'tollfree'  => '800123456',
                    'premium'   => '803123456',
                    'shared'    => '901123456',
                    'personal'  => '701234567',
                    'uan'       => '511234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'ET' => [
            'code'     => '251',
            'patterns' => [
                'example' => [
                    'fixed'     => '111112345',
                    'mobile'    => '911234567',
                    'emergency' => '991',
                ],
            ],
        ],
        'FI' => [
            'code'     => '358',
            'patterns' => [
                'example' => [
                    'fixed'     => '1312345678',
                    'mobile'    => '412345678',
                    'tollfree'  => '8001234567',
                    'premium'   => '600123456',
                    'uan'       => '10112345',
                    'emergency' => '112',
                ],
            ],
        ],
        'FJ' => [
            'code'     => '679',
            'patterns' => [
                'example' => [
                    'fixed'     => '3212345',
                    'mobile'    => '7012345',
                    'tollfree'  => '08001234567',
                    'shortcode' => '22',
                    'emergency' => '911',
                ],
            ],
        ],
        'FK' => [
            'code'     => '500',
            'patterns' => [
                'example' => [
                    'fixed'     => '31234',
                    'mobile'    => '51234',
                    'shortcode' => '123',
                    'emergency' => '999',
                ],
            ],
        ],
        'FM' => [
            'code'     => '691',
            'patterns' => [
                'example' => [
                    'fixed'     => '3201234',
                    'mobile'    => '3501234',
                    'emergency' => '911',
                ],
            ],
        ],
        'FO' => [
            'code'     => '298',
            'patterns' => [
                'example' => [
                    'fixed'     => '201234',
                    'mobile'    => '211234',
                    'tollfree'  => '802123',
                    'premium'   => '901123',
                    'voip'      => '601234',
                    'shortcode' => '114',
                    'emergency' => '112',
                ],
            ],
        ],
        'FR' => [
            'code'     => '33',
            'patterns' => [
                'example' => [
                    'fixed'     => '123456789',
                    'mobile' => ['612345678', '712345678',],
                    'tollfree'  => '801234567',
                    'premium'   => ['3123123456', '891123456', '897123456',],
                    'shared'    => ['810123456', '820123456',],
                    'voip'      => '912345678',
                    'emergency' => ['15', '17', '18', '112',],
                ],
                'invalid' => [
                    'fixed'     => ['0123456789', '1234567890', '12345678',],
                    'mobile'    => ['0612345678', '6123456780', '123456789', '6123456789',],
                    'tollfree'  => ['0801234567', '8012345670', '101234567', '811234567', '8012345678',],
                    'premium'   => ['31231234560', '03123123456', '2123123456', '894123456',],
                    'shared'    => ['812123456', '822123456', '830123456', '881123456', '891123456',],
                    'voip'      => ['123456789', '812345678', '9123456789',],
                    'emergency' => ['14', '16', '19', '20', '111', '113',],
                ],
            ],
        ],
        'GA' => [
            'code'     => '241',
            'patterns' => [
                'example' => [
                    'fixed'     => '1441234',
                    'mobile'    => '06031234',
                    'emergency' => '1730',
                ],
            ],
        ],
        'GB' => [
            'code'     => '44',
            'patterns' => [
                'example' => [
                    'fixed'     => '1212345678',
                    'mobile'    => '7400123456',
                    'pager'     => '7640123456',
                    'tollfree'  => '8001234567',
                    'premium'   => '9012345678',
                    'shared'    => '8431234567',
                    'personal'  => '7012345678',
                    'voip'      => '5612345678',
                    'uan'       => '5512345678',
                    'shortcode' => '150',
                    'emergency' => '112',
                ],
            ],
        ],
        'GD' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '4732691234',
                    'mobile'    => '4734031234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'GE' => [
            'code'     => '995',
            'patterns' => [
                'example' => [
                    'fixed'     => '322123456',
                    'mobile'    => '555123456',
                    'tollfree'  => '800123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'GF' => [
            'code'     => '594',
            'patterns' => [
                'example' => [
                    'fixed'     => '594101234',
                    'mobile'    => '694201234',
                    'emergency' => '15',
                ],
            ],
        ],
        'GG' => [
            'code'     => '44',
            'patterns' => [
                'example' => [
                    'fixed'     => '1481456789',
                    'mobile'    => '7781123456',
                    'pager'     => '7640123456',
                    'tollfree'  => '8001234567',
                    'premium'   => '9012345678',
                    'shared'    => '8431234567',
                    'personal'  => '7012345678',
                    'voip'      => '5612345678',
                    'uan'       => '5512345678',
                    'shortcode' => '155',
                    'emergency' => '999',
                ],
            ],
        ],
        'GH' => [
            'code'     => '233',
            'patterns' => [
                'example' => [
                    'fixed'     => '302345678',
                    'mobile'    => '231234567',
                    'tollfree'  => '80012345',
                    'emergency' => '999',
                ],
            ],
        ],
        'GI' => [
            'code'     => '350',
            'patterns' => [
                'example' => [
                    'fixed'     => '20012345',
                    'mobile'    => '57123456',
                    'tollfree'  => '80123456',
                    'premium'   => '88123456',
                    'shared'    => '87123456',
                    // wrong: 'shortcode' => '116123',
                    'emergency' => '112',
                ],
            ],
        ],
        'GL' => [
            'code'     => '299',
            'patterns' => [
                'example' => [
                    'fixed'     => '321000',
                    'mobile'    => '221234',
                    'tollfree'  => '801234',
                    'voip'      => '381234',
                    'emergency' => '112',
                ],
            ],
        ],
        'GM' => [
            'code'     => '220',
            'patterns' => [
                'example' => [
                    'fixed'     => '5661234',
                    'mobile'    => '3012345',
                    'emergency' => '117',
                ],
            ],
        ],
        'GN' => [
            'code'     => '224',
            'patterns' => [
                'example' => [
                    'fixed'  => '30241234',
                    'mobile' => '60201234',
                    'voip'   => '78123456',
                ],
            ],
        ],
        'GP' => [
            'code'     => '590',
            'patterns' => [
                'example' => [
                    'fixed'     => '590201234',
                    'mobile'    => '690301234',
                    'emergency' => '18',
                ],
            ],
        ],
        'GQ' => [
            'code'     => '240',
            'patterns' => [
                'example' => [
                    'fixed'    => '333091234',
                    'mobile'   => '222123456',
                    'tollfree' => '800123456',
                    'premium'  => '900123456',
                ],
            ],
        ],
        'GR' => [
            'code'     => '30',
            'patterns' => [
                'example' => [
                    'fixed'     => '2123456789',
                    'mobile'    => '6912345678',
                    'tollfree'  => '8001234567',
                    'premium'   => '9091234567',
                    'shared'    => '8011234567',
                    'personal'  => '7012345678',
                    'emergency' => '112',
                ],
            ],
        ],
        'GT' => [
            'code'     => '502',
            'patterns' => [
                'example' => [
                    'fixed'     => '22456789',
                    'mobile'    => '51234567',
                    'tollfree'  => '18001112222',
                    'premium'   => '19001112222',
                    'shortcode' => '124',
                    'emergency' => '110',
                ],
            ],
        ],
        'GU' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '6713001234',
                    'mobile'    => '6713001234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'GW' => [
            'code'     => '245',
            'patterns' => [
                'example' => [
                    'fixed'     => '3201234',
                    'mobile'    => '5012345',
                    'emergency' => '113',
                ],
            ],
        ],
        'GY' => [
            'code'     => '592',
            'patterns' => [
                'example' => [
                    'fixed'     => '2201234',
                    'mobile'    => '6091234',
                    'tollfree'  => '2891234',
                    'premium'   => '9008123',
                    'shortcode' => '0801',
                    'emergency' => '911',
                ],
            ],
        ],
        'HK' => [
            'code'     => '852',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '51234567',
                    'pager'     => '71234567',
                    'tollfree'  => '800123456',
                    'premium'   => '90012345678',
                    'personal'  => '81123456',
                    'emergency' => '999',
                ],
            ],
        ],
        'HN' => [
            'code'     => '504',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123456',
                    'mobile'    => '91234567',
                    'emergency' => '199',
                ],
            ],
        ],
        'HR' => [
            'code'     => '385',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'uan'       => '62123456',
                    'mobile'    => '912345678',
                    'tollfree'  => '8001234567',
                    'premium'   => '611234',
                    'personal'  => '741234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'HT' => [
            'code'     => '509',
            'patterns' => [
                'example' => [
                    'fixed'     => '22453300',
                    'mobile'    => '34101234',
                    'tollfree'  => '80012345',
                    'voip'      => '98901234',
                    'shortcode' => '114',
                    'emergency' => '118',
                ],
            ],
        ],
        'HU' => [
            'code'     => '36',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'mobile'    => '201234567',
                    'tollfree'  => '80123456',
                    'premium'   => '90123456',
                    'shared'    => '40123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'ID' => [
            'code'     => '62',
            'patterns' => [
                'example' => [
                    'fixed'     => '612345678',
                    'mobile'    => '812345678',
                    'tollfree'  => '8001234567',
                    'premium'   => '8091234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'IE' => [
            'code'     => '353',
            'patterns' => [
                'example' => [
                    'fixed'     => '2212345',
                    'mobile'    => '850123456',
                    'tollfree'  => '1800123456',
                    'premium'   => '1520123456',
                    'shared'    => '1850123456',
                    'personal'  => '700123456',
                    'voip'      => '761234567',
                    'uan'       => '818123456',
                    'voicemail' => '8501234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'IL' => [
            'code'     => '972',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '501234567',
                    'tollfree'  => '1800123456',
                    'premium'   => '1919123456',
                    'shared'    => '1700123456',
                    'voip'      => '771234567',
                    'uan'       => '2250',
                    'voicemail' => '1599123456',
                    'shortcode' => '1455',
                    'emergency' => '112',
                ],
            ],
        ],
        'IM' => [
            'code'     => '44',
            'patterns' => [
                'example' => [
                    'fixed'     => '1624456789',
                    'mobile'    => '7924123456',
                    'tollfree'  => '8081624567',
                    'premium'   => '9016247890',
                    'shared'    => '8456247890',
                    'personal'  => '7012345678',
                    'voip'      => '5612345678',
                    'uan'       => '5512345678',
                    'shortcode' => '150',
                    'emergency' => '999',
                ],
            ],
        ],
        'IN' => [
            'code'     => '91',
            'patterns' => [
                'example' => [
                    'fixed'     => '1123456789',
                    'mobile'    => '9123456789',
                    'tollfree'  => '1800123456',
                    'premium'   => '1861123456789',
                    'uan'       => '18603451234',
                    'emergency' => '108',
                ],
            ],
        ],
        'IO' => [
            'code'     => '246',
            'patterns' => [
                'example' => [
                    'fixed'  => '3709100',
                    'mobile' => '3801234',
                ],
            ],
        ],
        'IQ' => [
            'code'     => '964',
            'patterns' => [
                'example' => [
                    'fixed'  => '12345678',
                    'mobile' => '7912345678',
                ],
            ],
        ],
        'IR' => [
            'code'     => '98',
            'patterns' => [
                'example' => [
                    'fixed'     => '2123456789',
                    'mobile'    => '9123456789',
                    'pager'     => '9432123456',
                    'voip'      => '9932123456',
                    'uan'       => '9990123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'IS' => [
            'code'     => '354',
            'patterns' => [
                'example' => [
                    'fixed'     => '4101234',
                    'mobile'    => '6101234',
                    'tollfree'  => '8001234',
                    'premium'   => '9011234',
                    'voip'      => '4921234',
                    'voicemail' => '388123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'IT' => [
            'code'     => '39',
            'patterns' => [
                'example' => [
                    'fixed'     => '0212345678',
                    'mobile'    => '3123456789',
                    'tollfree'  => '800123456',
                    'premium'   => '899123456',
                    'shared'    => '848123456',
                    'personal'  => '1781234567',
                    'voip'      => '5512345678',
                    'shortcode' => '114',
                    'emergency' => '112',
                ],
            ],
        ],
        'JE' => [
            'code'     => '44',
            'patterns' => [
                'example' => [
                    'fixed'     => '1534456789',
                    'mobile'    => '7797123456',
                    'pager'     => '7640123456',
                    'tollfree'  => '8007354567',
                    'premium'   => '9018105678',
                    'shared'    => '8447034567',
                    'personal'  => '7015115678',
                    'voip'      => '5612345678',
                    'uan'       => '5512345678',
                    'shortcode' => '150',
                    'emergency' => '999',
                ],
            ],
        ],
        'JM' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '8765123456',
                    'mobile'    => '8762101234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '119',
                ],
            ],
        ],
        'JO' => [
            'code'     => '962',
            'patterns' => [
                'example' => [
                    'fixed'     => '62001234',
                    'mobile'    => '790123456',
                    'pager'     => '746612345',
                    'tollfree'  => '80012345',
                    'premium'   => '90012345',
                    'shared'    => '85012345',
                    'personal'  => '700123456',
                    'uan'       => '88101234',
                    'shortcode' => '111',
                    'emergency' => '112',
                ],
            ],
        ],
        'JP' => [
            'code'     => '81',
            'patterns' => [
                'example' => [
                    'fixed'     => '312345678',
                    'mobile'    => '7012345678',
                    'pager'     => '2012345678',
                    'tollfree'  => '120123456',
                    'premium'   => '990123456',
                    'personal'  => '601234567',
                    'voip'      => '5012345678',
                    'uan'       => '570123456',
                    'emergency' => '110',
                ],
            ],
        ],
        'KE' => [
            'code'     => '254',
            'patterns' => [
                'example' => [
                    'fixed'     => '202012345',
                    'mobile'    => '712123456',
                    'tollfree'  => '800223456',
                    'premium'   => '900223456',
                    'shortcode' => '116',
                    'emergency' => '999',
                ],
            ],
        ],
        'KG' => [
            'code'     => '996',
            'patterns' => [
                'example' => [
                    'fixed'     => '312123456',
                    'mobile'    => '700123456',
                    'tollfree'  => '800123456',
                    'emergency' => '101',
                ],
            ],
        ],
        'KH' => [
            'code'     => '855',
            'patterns' => [
                'example' => [
                    'fixed'     => '23456789',
                    'mobile'    => '91234567',
                    'tollfree'  => '1800123456',
                    'premium'   => '1900123456',
                    'emergency' => '117',
                ],
            ],
        ],
        'KI' => [
            'code'     => '686',
            'patterns' => [
                'example' => [
                    'fixed'     => '31234',
                    'mobile'    => '61234',
                    'shortcode' => '100',
                    'emergency' => '999',
                ],
            ],
        ],
        'KM' => [
            'code'     => '269',
            'patterns' => [
                'example' => [
                    'fixed'     => '7712345',
                    'mobile'    => '3212345',
                    'premium'   => '9001234',
                    'emergency' => '17',
                ],
            ],
        ],
        'KN' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '8692361234',
                    'mobile'    => '8695561234',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '999',
                ],
            ],
        ],
        'KP' => [
            'code'     => '850',
            'patterns' => [
                'example' => [
                    'fixed'  => '21234567',
                    'mobile' => '1921234567',
                ],
            ],
        ],
        'KR' => [
            'code'     => '82',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123456',
                    'mobile'    => '1023456789',
                    'tollfree'  => '801234567',
                    'premium'   => '602345678',
                    'personal'  => '5012345678',
                    'voip'      => '7012345678',
                    'uan'       => '15441234',
                    'emergency' => '112',
                ],
            ],
        ],
        'KW' => [
            'code'     => '965',
            'patterns' => [
                'example' => [
                    'fixed'     => '22345678',
                    'mobile'    => '50012345',
                    'shortcode' => '177',
                    'emergency' => '112',
                ],
            ],
        ],
        'KY' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '3452221234',
                    'mobile'    => '3453231234',
                    'pager'     => '3458491234',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'KZ' => [
            'code'     => '7',
            'patterns' => [
                'example' => [
                    'fixed'     => '7123456789',
                    'mobile'    => '7710009998',
                    'tollfree'  => '8001234567',
                    'premium'   => '8091234567',
                    'voip'      => '7511234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'LA' => [
            'code'     => '856',
            'patterns' => [
                'example' => [
                    'fixed'     => '21212862',
                    'mobile'    => '2023123456',
                    'emergency' => '190',
                ],
            ],
        ],
        'LB' => [
            'code'     => '961',
            'patterns' => [
                'example' => [
                    'fixed'     => '1123456',
                    'mobile'    => '71123456',
                    'premium'   => '90123456',
                    'shared'    => '80123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'LC' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '7582345678',
                    'mobile'    => '7582845678',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'LI' => [
            'code'     => '423',
            'patterns' => [
                'example' => [
                    'fixed'     => '2345678',
                    'mobile'    => '661234567',
                    'tollfree'  => '8002222',
                    'premium'   => '9002222',
                    'uan'       => '8770123',
                    'voicemail' => '697361234',
                    'personal'  => '7011234',
                    'shortcode' => '1600',
                    'emergency' => '112',
                ],
            ],
        ],
        'LK' => [
            'code'     => '94',
            'patterns' => [
                'example' => [
                    'fixed'     => '112345678',
                    'mobile'    => '712345678',
                    'emergency' => '119',
                ],
            ],
        ],
        'LR' => [
            'code'     => '231',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '4612345',
                    'premium'   => '90123456',
                    'voip'      => '332001234',
                    'emergency' => '911',
                ],
            ],
        ],
        'LS' => [
            'code'     => '266',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123456',
                    'mobile'    => '50123456',
                    'tollfree'  => '80021234',
                    'emergency' => '112',
                ],
            ],
        ],
        'LT' => [
            'code'     => '370',
            'patterns' => [
                'example' => [
                    'fixed'     => '31234567',
                    'mobile'    => '61234567',
                    'tollfree'  => '80012345',
                    'premium'   => '90012345',
                    'personal'  => '70012345',
                    'shared'    => '80812345',
                    'uan'       => '70712345',
                    'emergency' => '112',
                ],
            ],
        ],
        'LU' => [
            'code'     => '352',
            'patterns' => [
                'example' => [
                    'fixed'     => '27123456',
                    'mobile'    => '628123456',
                    'tollfree'  => '80012345',
                    'premium'   => '90012345',
                    'shared'    => '80112345',
                    'personal'  => '70123456',
                    'voip'      => '2012345',
                    'shortcode' => '12123',
                    'emergency' => '112',
                ],
            ],
        ],
        'LV' => [
            'code'     => '371',
            'patterns' => [
                'example' => [
                    'fixed'     => '63123456',
                    'mobile'    => '21234567',
                    'tollfree'  => '80123456',
                    'premium'   => '90123456',
                    'shared'    => '81123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'LY' => [
            'code'     => '218',
            'patterns' => [
                'example' => [
                    'fixed'     => '212345678',
                    'mobile'    => '912345678',
                    'emergency' => '193',
                ],
            ],
        ],
        'MA' => [
            'code'     => '212',
            'patterns' => [
                'example' => [
                    'fixed'     => '520123456',
                    'mobile'    => '650123456',
                    'tollfree'  => '801234567',
                    'premium'   => '891234567',
                    'emergency' => '15',
                ],
            ],
        ],
        'MC' => [
            'code'     => '377',
            'patterns' => [
                'example' => [
                    'fixed'     => '99123456',
                    'mobile'    => '612345678',
                    'tollfree'  => '90123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'MD' => [
            'code'     => '373',
            'patterns' => [
                'example' => [
                    'fixed'     => '22212345',
                    'mobile'    => '65012345',
                    'tollfree'  => '80012345',
                    'premium'   => '90012345',
                    'shared'    => '80812345',
                    'uan'       => '80312345',
                    'voip'      => '30123456',
                    'shortcode' => '116000',
                    'emergency' => '112',
                ],
            ],
        ],
        'ME' => [
            'code'     => '382',
            'patterns' => [
                'example' => [
                    'fixed'     => '30234567',
                    'mobile'    => '67622901',
                    'tollfree'  => '80080002',
                    'premium'   => '94515151',
                    'voip'      => '78108780',
                    'uan'       => '77273012',
                    'shortcode' => '1011',
                    'emergency' => '112',
                ],
            ],
        ],
        'MF' => [
            'code'     => '590',
            'patterns' => [
                'example' => [
                    'fixed'     => '590271234',
                    'mobile'    => '690221234',
                    'emergency' => '18',
                ],
            ],
        ],
        'MG' => [
            'code'     => '261',
            'patterns' => [
                'example' => [
                    'fixed'     => '202123456',
                    'mobile'    => '301234567',
                    'emergency' => '117',
                ],
            ],
        ],
        'MH' => [
            'code'     => '692',
            'patterns' => [
                'example' => [
                    'fixed'  => '2471234',
                    'mobile' => '2351234',
                    'voip'   => '6351234',
                ],
            ],
        ],
        'MK' => [
            'code'     => '389',
            'patterns' => [
                'example' => [
                    'fixed'     => '22212345',
                    'mobile'    => '72345678',
                    'tollfree'  => '80012345',
                    'premium'   => '50012345',
                    'shared'    => '80123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'ML' => [
            'code'     => '223',
            'patterns' => [
                'example' => [
                    'fixed'     => '20212345',
                    'mobile'    => '65012345',
                    'tollfree'  => '80012345',
                    'emergency' => '17',
                ],
            ],
        ],
        'MM' => [
            'code'     => '95',
            'patterns' => [
                'example' => [
                    'fixed'     => '1234567',
                    'mobile'    => '92123456',
                    'voip'      => '13331234',
                    'emergency' => '199',
                ],
            ],
        ],
        'MN' => [
            'code'     => '976',
            'patterns' => [
                'example' => [
                    'fixed'     => '50123456',
                    'mobile'    => '88123456',
                    'voip'      => '75123456',
                    'emergency' => '102',
                ],
            ],
        ],
        'MO' => [
            'code'     => '853',
            'patterns' => [
                'example' => [
                    'fixed'     => '28212345',
                    'mobile'    => '66123456',
                    'emergency' => '999',
                ],
            ],
        ],
        'MP' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '6702345678',
                    'mobile'    => '6702345678',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'MQ' => [
            'code'     => '596',
            'patterns' => [
                'example' => [
                    'fixed'     => '596301234',
                    'mobile'    => '696201234',
                    'emergency' => '15',
                ],
            ],
        ],
        'MR' => [
            'code'     => '222',
            'patterns' => [
                'example' => [
                    'fixed'     => '35123456',
                    'mobile'    => '22123456',
                    'tollfree'  => '80012345',
                    'emergency' => '17',
                ],
            ],
        ],
        'MS' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '6644912345',
                    'mobile'    => '6644923456',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'MT' => [
            'code'     => '356',
            'patterns' => [
                'example' => [
                    'fixed'     => '21001234',
                    'mobile'    => '96961234',
                    'pager'     => '71171234',
                    'premium'   => '50031234',
                    'emergency' => '112',
                ],
            ],
        ],
        'MU' => [
            'code'     => '230',
            'patterns' => [
                'example' => [
                    'fixed'     => '2012345',
                    'mobile'    => '2512345',
                    'pager'     => '2181234',
                    'tollfree'  => '8001234',
                    'premium'   => '3012345',
                    'voip'      => '3201234',
                    'shortcode' => '195',
                    'emergency' => '999',
                ],
            ],
        ],
        'MV' => [
            'code'     => '960',
            'patterns' => [
                'example' => [
                    'fixed'     => '6701234',
                    'mobile'    => '7712345',
                    'pager'     => '7812345',
                    'premium'   => '9001234567',
                    'shortcode' => '123',
                    'emergency' => '102',
                ],
            ],
        ],
        'MW' => [
            'code'     => '265',
            'patterns' => [
                'example' => [
                    'fixed'     => '1234567',
                    'mobile'    => '991234567',
                    'emergency' => '997',
                ],
            ],
        ],
        'MX' => [
            'code'     => '52',
            'patterns' => [
                'example' => [
                    'fixed'     => '2221234567',
                    'mobile'    => '12221234567',
                    'tollfree'  => '8001234567',
                    'premium'   => '9001234567',
                    'emergency' => '066',
                ],
            ],
        ],
        'MY' => [
            'code'     => '60',
            'patterns' => [
                'example' => [
                    'fixed'     => '323456789',
                    'mobile'    => '123456789',
                    'tollfree'  => '1300123456',
                    'premium'   => '1600123456',
                    'personal'  => '1700123456',
                    'voip'      => '1541234567',
                    'emergency' => '999',
                ],
            ],
        ],
        'MZ' => [
            'code'     => '258',
            'patterns' => [
                'example' => [
                    'fixed'     => '21123456',
                    'mobile'    => '821234567',
                    'tollfree'  => '800123456',
                    'shortcode' => '101',
                    'emergency' => '119',
                ],
            ],
        ],
        'NA' => [
            'code'     => '264',
            'patterns' => [
                'example' => [
                    'fixed'     => '612012345',
                    'mobile'    => '811234567',
                    'premium'   => '870123456',
                    'voip'      => '88612345',
                    'shortcode' => '93111',
                    'emergency' => '10111',
                ],
            ],
        ],
        'NC' => [
            'code'     => '687',
            'patterns' => [
                'example' => [
                    'fixed'     => '201234',
                    'mobile'    => '751234',
                    'premium'   => '366711',
                    'shortcode' => '1000',
                    'emergency' => '15',
                ],
            ],
        ],
        'NE' => [
            'code'     => '227',
            'patterns' => [
                'example' => [
                    'fixed'    => '20201234',
                    'mobile'   => '93123456',
                    'tollfree' => '08123456',
                    'premium'  => '09123456',
                ],
            ],
        ],
        'NF' => [
            'code'     => '672',
            'patterns' => [
                'example' => [
                    'fixed'     => '106609',
                    'mobile'    => '381234',
                    'emergency' => '911',
                ],
            ],
        ],
        'NG' => [
            'code'     => '234',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'mobile'    => '8021234567',
                    'tollfree'  => '80017591759',
                    'uan'       => '7001234567',
                    'emergency' => '199',
                ],
            ],
        ],
        'NI' => [
            'code'     => '505',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => ['81234567', '71234567'],
                    'tollfree'  => '18001234',
                    'emergency' => '118',
                ],
            ],
        ],
        'NL' => [
            'code'     => '31',
            'patterns' => [
                'example' => [
                    'fixed'     => '101234567',
                    'mobile'    => '612345678',
                    'pager'     => '662345678',
                    'tollfree'  => '8001234',
                    'premium'   => '9001234',
                    'voip'      => '851234567',
                    'uan'       => '14020',
                    'shortcode' => '1833',
                    'emergency' => '112',
                ],
            ],
        ],
        'NO' => [
            'code'     => '47',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '41234567',
                    'tollfree'  => '80012345',
                    'premium'   => '82012345',
                    'shared'    => '81021234',
                    'personal'  => '88012345',
                    'voip'      => '85012345',
                    'uan'       => '01234',
                    'voicemail' => '81212345',
                    'emergency' => '112',
                ],
            ],
        ],
        'NP' => [
            'code'     => '977',
            'patterns' => [
                'example' => [
                    'fixed'     => '14567890',
                    'mobile'    => '9841234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'NR' => [
            'code'     => '674',
            'patterns' => [
                'example' => [
                    'fixed'     => '4441234',
                    'mobile'    => '5551234',
                    'shortcode' => '123',
                    'emergency' => '110',
                ],
            ],
        ],
        'NU' => [
            'code'     => '683',
            'patterns' => [
                'example' => [
                    'fixed'     => '4002',
                    'mobile'    => '1234',
                    'emergency' => '999',
                ],
            ],
        ],
        'NZ' => [
            'code'     => '64',
            'patterns' => [
                'example' => [
                    'fixed'     => '32345678',
                    'mobile'    => '211234567',
                    'pager'     => '26123456',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'emergency' => '111',
                ],
            ],
        ],
        'OM' => [
            'code'     => '968',
            'patterns' => [
                'example' => [
                    'fixed'     => '23123456',
                    'mobile'    => '92123456',
                    'tollfree'  => '80071234',
                    'emergency' => '9999',
                ],
            ],
        ],
        'PA' => [
            'code'     => '507',
            'patterns' => [
                'example' => [
                    'fixed'     => '2001234',
                    'mobile'    => '60012345',
                    'tollfree'  => '8001234',
                    'premium'   => '8601234',
                    'shortcode' => '102',
                    'emergency' => '911',
                ],
            ],
        ],
        'PE' => [
            'code'     => '51',
            'patterns' => [
                'example' => [
                    'fixed'     => '11234567',
                    'mobile'    => '912345678',
                    'tollfree'  => '80012345',
                    'premium'   => '80512345',
                    'shared'    => '80112345',
                    'personal'  => '80212345',
                    'emergency' => '105',
                ],
            ],
        ],
        'PF' => [
            'code'     => '689',
            'patterns' => [
                'example' => [
                    'fixed'     => '401234',
                    'mobile'    => '212345',
                    'emergency' => '15',
                ],
            ],
        ],
        'PG' => [
            'code'     => '675',
            'patterns' => [
                'example' => [
                    'fixed'     => '3123456',
                    'mobile'    => '6812345',
                    'tollfree'  => '1801234',
                    'voip'      => '2751234',
                    'emergency' => '000',
                ],
            ],
        ],
        'PH' => [
            'code'     => '63',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '9051234567',
                    'tollfree'  => '180012345678',
                    'emergency' => '117',
                ],
            ],
        ],
        'PK' => [
            'code'     => '92',
            'patterns' => [
                'example' => [
                    'fixed'     => '2123456789',
                    'mobile'    => '3012345678',
                    'tollfree'  => '80012345',
                    'premium'   => '90012345',
                    'personal'  => '122044444',
                    'uan'       => '21111825888',
                    'emergency' => '112',
                ],
            ],
        ],
        'PL' => [
            'code'     => '48',
            'patterns' => [
                'example' => [
                    'fixed'     => '123456789',
                    'mobile'    => '512345678',
                    'pager'     => '642123456',
                    'tollfree'  => '800123456',
                    'premium'   => '701234567',
                    'shared'    => '801234567',
                    'voip'      => '391234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'PM' => [
            'code'     => '508',
            'patterns' => [
                'example' => [
                    'fixed'     => '411234',
                    'mobile'    => '551234',
                    'emergency' => '17',
                ],
            ],
        ],
        'PR' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '7872345678',
                    'mobile'    => '7872345678',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'PS' => [
            'code'     => '970',
            'patterns' => [
                'example' => [
                    'fixed'    => '22234567',
                    'mobile'   => '599123456',
                    'tollfree' => '1800123456',
                    'premium'  => '19123',
                    'shared'   => '1700123456',
                ],
            ],
        ],
        'PT' => [
            'code'     => '351',
            'patterns' => [
                'example' => [
                    'fixed'     => '212345678',
                    'mobile'    => '912345678',
                    'tollfree'  => '800123456',
                    'premium'   => '760123456',
                    'shared'    => '808123456',
                    'personal'  => '884123456',
                    'voip'      => '301234567',
                    'uan'       => '707123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'PW' => [
            'code'     => '680',
            'patterns' => [
                'example' => [
                    'fixed'     => '2771234',
                    'mobile'    => '6201234',
                    'emergency' => '911',
                ],
            ],
        ],
        'PY' => [
            'code'     => '595',
            'patterns' => [
                'example' => [
                    'fixed'     => '212345678',
                    'mobile'    => '961456789',
                    'voip'      => '870012345',
                    'uan'       => '201234567',
                    'shortcode' => '123',
                    'emergency' => '911',
                ],
            ],
        ],
        'QA' => [
            'code'     => '974',
            'patterns' => [
                'example' => [
                    'fixed'     => '44123456',
                    'mobile'    => '33123456',
                    'pager'     => '2123456',
                    'tollfree'  => '8001234',
                    'shortcode' => '2012',
                    'emergency' => '999',
                ],
            ],
        ],
        'RE' => [
            'code'     => '262',
            'patterns' => [
                'example' => [
                    'fixed'     => '262161234',
                    'mobile'    => '692123456',
                    'tollfree'  => '801234567',
                    'premium'   => '891123456',
                    'shared'    => '810123456',
                    'emergency' => '15',
                ],
            ],
        ],
        'RO' => [
            'code'     => '40',
            'patterns' => [
                'example' => [
                    'fixed'     => '211234567',
                    'mobile'    => '712345678',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'shared'    => '801123456',
                    'personal'  => '802123456',
                    'uan'       => '372123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'RS' => [
            'code'     => '381',
            'patterns' => [
                'example' => [
                    'fixed'     => '10234567',
                    'mobile'    => '601234567',
                    'tollfree'  => '80012345',
                    'premium'   => '90012345',
                    'uan'       => '700123456',
                    'shortcode' => '18923',
                    'emergency' => '112',
                ],
            ],
        ],
        'RU' => [
            'code'     => '7',
            'patterns' => [
                'example' => [
                    'fixed'     => '3011234567',
                    'mobile'    => '9123456789',
                    'tollfree'  => '8001234567',
                    'premium'   => '8091234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'RW' => [
            'code'     => '250',
            'patterns' => [
                'example' => [
                    'fixed'     => '250123456',
                    'mobile'    => '720123456',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'SA' => [
            'code'     => '966',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'mobile'    => '512345678',
                    'tollfree'  => '8001234567',
                    'uan'       => '920012345',
                    'shortcode' => '902',
                    'emergency' => '999',
                ],
            ],
        ],
        'SB' => [
            'code'     => '677',
            'patterns' => [
                'example' => [
                    'fixed'     => '40123',
                    'mobile'    => '7421234',
                    'tollfree'  => '18123',
                    'voip'      => '51123',
                    'shortcode' => '100',
                    'emergency' => '999',
                ],
            ],
        ],
        'SC' => [
            'code'     => '248',
            'patterns' => [
                'example' => [
                    'fixed'     => '4217123',
                    'mobile'    => '2510123',
                    'tollfree'  => '800000',
                    'premium'   => '981234',
                    'voip'      => '6412345',
                    'shortcode' => '100',
                    'emergency' => '999',
                ],
            ],
        ],
        'SD' => [
            'code'     => '249',
            'patterns' => [
                'example' => [
                    'fixed'     => '121231234',
                    'mobile'    => '911231234',
                    'emergency' => '999',
                ],
            ],
        ],
        'SE' => [
            'code'     => '46',
            'patterns' => [
                'example' => [
                    'fixed'     => '8123456',
                    'mobile'    => '701234567',
                    'pager'     => '741234567',
                    'tollfree'  => '201234567',
                    'premium'   => '9001234567',
                    'shared'    => '771234567',
                    'personal'  => '751234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'SG' => [
            'code'     => '65',
            'patterns' => [
                'example' => [
                    'fixed'     => '61234567',
                    'mobile'    => '81234567',
                    'tollfree'  => '18001234567',
                    'premium'   => '19001234567',
                    'voip'      => '31234567',
                    'uan'       => '70001234567',
                    'shortcode' => '1312',
                    'emergency' => '999',
                ],
            ],
        ],
        'SH' => [
            'code'     => '290',
            'patterns' => [
                'example' => [
                    'fixed'     => '2158',
                    'premium'   => '5012',
                    'shortcode' => '1234',
                    'emergency' => '999',
                ],
            ],
        ],
        'SI' => [
            'code'     => '386',
            'patterns' => [
                'example' => [
                    'fixed'     => '11234567',
                    'mobile'    => '31234567',
                    'tollfree'  => '80123456',
                    'premium'   => '90123456',
                    'voip'      => '59012345',
                    'emergency' => '112',
                ],
            ],
        ],
        'SJ' => [
            'code'     => '47',
            'patterns' => [
                'example' => [
                    'fixed'     => '79123456',
                    'mobile'    => '41234567',
                    'tollfree'  => '80012345',
                    'premium'   => '82012345',
                    'shared'    => '81021234',
                    'personal'  => '88012345',
                    'voip'      => '85012345',
                    'uan'       => '01234',
                    'voicemail' => '81212345',
                    'emergency' => '112',
                ],
            ],
        ],
        'SK' => [
            'code'     => '421',
            'patterns' => [
                'example' => [
                    'fixed'     => '212345678',
                    'mobile'    => '912123456',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'shared'    => '850123456',
                    'voip'      => '690123456',
                    'uan'       => '961234567',
                    'emergency' => '112',
                ],
            ],
        ],
        'SL' => [
            'code'     => '232',
            'patterns' => [
                'example' => [
                    'fixed'     => '22221234',
                    'mobile'    => '25123456',
                    'emergency' => '999',
                ],
            ],
        ],
        'SM' => [
            'code'     => '378',
            'patterns' => [
                'example' => [
                    'fixed'     => '0549886377',
                    'mobile'    => '66661212',
                    'premium'   => '71123456',
                    'voip'      => '58001110',
                    'emergency' => '113',
                ],
            ],
        ],
        'SN' => [
            'code'     => '221',
            'patterns' => [
                'example' => [
                    'fixed'  => '301012345',
                    'mobile' => '701012345',
                    'voip'   => '333011234',
                ],
            ],
        ],
        'SO' => [
            'code'     => '252',
            'patterns' => [
                'example' => [
                    'fixed'  => '5522010',
                    'mobile' => '90792024',
                ],
            ],
        ],
        'SR' => [
            'code'     => '597',
            'patterns' => [
                'example' => [
                    'fixed'     => '211234',
                    'mobile'    => '7412345',
                    'voip'      => '561234',
                    'shortcode' => '1234',
                    'emergency' => '115',
                ],
            ],
        ],
        'SS' => [
            'code'     => '211',
            'patterns' => [
                'example' => [
                    'fixed'  => '181234567',
                    'mobile' => '977123456',
                ],
            ],
        ],
        'ST' => [
            'code'     => '239',
            'patterns' => [
                'example' => [
                    'fixed'     => '2221234',
                    'mobile'    => '9812345',
                    'emergency' => '112',
                ],
            ],
        ],
        'SV' => [
            'code'     => '503',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '70123456',
                    'tollfree'  => '8001234',
                    'premium'   => '9001234',
                    'emergency' => '911',
                ],
            ],
        ],
        'SX' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '7215425678',
                    'mobile'    => '7215205678',
                    'tollfree'  => '8002123456',
                    'premium'   => '9002123456',
                    'personal'  => '5002345678',
                    'emergency' => '919',
                ],
            ],
        ],
        'SY' => [
            'code'     => '963',
            'patterns' => [
                'example' => [
                    'fixed'     => '112345678',
                    'mobile'    => '944567890',
                    'emergency' => '112',
                ],
            ],
        ],
        'SZ' => [
            'code'     => '268',
            'patterns' => [
                'example' => [
                    'fixed'     => '22171234',
                    'mobile'    => '76123456',
                    'tollfree'  => '08001234',
                    'emergency' => '999',
                ],
            ],
        ],
        'TC' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '6497121234',
                    'mobile'    => '6492311234',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'voip'      => '6497101234',
                    'emergency' => '911',
                ],
            ],
        ],
        'TD' => [
            'code'     => '235',
            'patterns' => [
                'example' => [
                    'fixed'     => '22501234',
                    'mobile'    => '63012345',
                    'emergency' => '17',
                ],
            ],
        ],
        'TG' => [
            'code'     => '228',
            'patterns' => [
                'example' => [
                    'fixed'     => '22212345',
                    'mobile'    => '90112345',
                    'emergency' => '117',
                ],
            ],
        ],
        'TH' => [
            'code'     => '66',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '812345678',
                    'tollfree'  => '1800123456',
                    'premium'   => '1900123456',
                    'voip'      => '601234567',
                    'uan'       => '1100',
                    'emergency' => '191',
                ],
            ],
        ],
        'TJ' => [
            'code'     => '992',
            'patterns' => [
                'example' => [
                    'fixed'     => '372123456',
                    'mobile'    => '917123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'TK' => [
            'code'     => '690',
            'patterns' => [
                'example' => [
                    'fixed'  => '3010',
                    'mobile' => '5190',
                ],
            ],
        ],
        'TL' => [
            'code'     => '670',
            'patterns' => [
                'example' => [
                    'fixed'     => '2112345',
                    'mobile'    => '77212345',
                    'tollfree'  => '8012345',
                    'premium'   => '9012345',
                    'personal'  => '7012345',
                    'shortcode' => '102',
                    'emergency' => '112',
                ],
            ],
        ],
        'TM' => [
            'code'     => '993',
            'patterns' => [
                'example' => [
                    'fixed'     => '12345678',
                    'mobile'    => '66123456',
                    'emergency' => '03',
                ],
            ],
        ],
        'TN' => [
            'code'     => '216',
            'patterns' => [
                'example' => [
                    'fixed'     => '71234567',
                    'mobile'    => '20123456',
                    'premium'   => '80123456',
                    'emergency' => '197',
                ],
            ],
        ],
        'TO' => [
            'code'     => '676',
            'patterns' => [
                'example' => [
                    'fixed'     => '20123',
                    'mobile'    => '7715123',
                    'tollfree'  => '0800222',
                    'emergency' => '911',
                ],
            ],
        ],
        'TR' => [
            'code'     => '90',
            'patterns' => [
                'example' => [
                    'fixed'     => '2123456789',
                    'mobile'    => '5012345678',
                    'pager'     => '5123456789',
                    'tollfree'  => '8001234567',
                    'premium'   => '9001234567',
                    'uan'       => '4441444',
                    'emergency' => '112',
                ],
            ],
        ],
        'TT' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '8682211234',
                    'mobile'    => '8682911234',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'emergency' => '999',
                ],
            ],
        ],
        'TV' => [
            'code'     => '688',
            'patterns' => [
                'example' => [
                    'fixed'     => '20123',
                    'mobile'    => '901234',
                    'emergency' => '911',
                ],
            ],
        ],
        'TW' => [
            'code'     => '886',
            'patterns' => [
                'example' => [
                    'fixed'     => '21234567',
                    'mobile'    => '912345678',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'emergency' => '110',
                ],
            ],
        ],
        'TZ' => [
            'code'     => '255',
            'patterns' => [
                'example' => [
                    'fixed'     => '222345678',
                    'mobile'    => '612345678',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'shared'    => '840123456',
                    'voip'      => '412345678',
                    'emergency' => '111',
                ],
            ],
        ],
        'UA' => [
            'code'     => '380',
            'patterns' => [
                'example' => [
                    'fixed'     => '311234567',
                    'mobile'    => '391234567',
                    'tollfree'  => '800123456',
                    'premium'   => '900123456',
                    'emergency' => '112',
                ],
            ],
        ],
        'UG' => [
            'code'     => '256',
            'patterns' => [
                'example' => [
                    'fixed'     => '312345678',
                    'mobile'    => '712345678',
                    'tollfree'  => '800123456',
                    'premium'   => '901123456',
                    'emergency' => '999',
                ],
            ],
        ],
        'US' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '2015550123',
                    'mobile'    => '2015550123',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'UY' => [
            'code'     => '598',
            'patterns' => [
                'example' => [
                    'fixed'     => '21231234',
                    'mobile'    => '94231234',
                    'tollfree'  => '8001234',
                    'premium'   => '9001234',
                    'shortcode' => '104',
                    'emergency' => '911',
                ],
            ],
        ],
        'UZ' => [
            'code'     => '998',
            'patterns' => [
                'example' => [
                    'fixed'     => '662345678',
                    'mobile'    => '912345678',
                    'emergency' => '01',
                ],
            ],
        ],
        'VA' => [
            'code'     => '379',
            'patterns' => [
                'example' => [
                    'fixed'     => '0669812345',
                    'emergency' => '113',
                ],
            ],
        ],
        'VC' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '7842661234',
                    'mobile'    => '7844301234',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'VE' => [
            'code'     => '58',
            'patterns' => [
                'example' => [
                    'fixed'     => '2121234567',
                    'mobile'    => '4121234567',
                    'tollfree'  => '8001234567',
                    'premium'   => '9001234567',
                    'emergency' => '171',
                ],
            ],
        ],
        'VG' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '2842291234',
                    'mobile'    => '2843001234',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'VI' => [
            'code'     => '1',
            'patterns' => [
                'example' => [
                    'fixed'     => '3406421234',
                    'mobile'    => '3406421234',
                    'tollfree'  => '8002345678',
                    'premium'   => '9002345678',
                    'personal'  => '5002345678',
                    'emergency' => '911',
                ],
            ],
        ],
        'VN' => [
            'code'     => '84',
            'patterns' => [
                'example' => [
                    'fixed'     => '2101234567',
                    'mobile'    => '912345678',
                    'tollfree'  => '1800123456',
                    'premium'   => '1900123456',
                    'uan'       => '1992000',
                    'emergency' => '113',
                ],
            ],
        ],
        'VU' => [
            'code'     => '678',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123',
                    'mobile'    => '5912345',
                    'uan'       => '30123',
                    'emergency' => '112',
                ],
            ],
        ],
        'WF' => [
            'code'     => '681',
            'patterns' => [
                'example' => [
                    'fixed'     => '501234',
                    'mobile'    => '501234',
                    'emergency' => '15',
                ],
            ],
        ],
        'WS' => [
            'code'     => '685',
            'patterns' => [
                'example' => [
                    'fixed'     => '22123',
                    'mobile'    => '601234',
                    'tollfree'  => '800123',
                    'emergency' => '994',
                ],
            ],
        ],
        'YE' => [
            'code'     => '967',
            'patterns' => [
                'example' => [
                    'fixed'     => '1234567',
                    'mobile'    => '712345678',
                    'emergency' => '191',
                ],
            ],
        ],
        'YT' => [
            'code'     => '262',
            'patterns' => [
                'example' => [
                    'fixed'     => '269601234',
                    'mobile'    => '639123456',
                    'tollfree'  => '801234567',
                    'emergency' => '15',
                ],
            ],
        ],
        'ZA' => [
            'code'     => '27',
            'patterns' => [
                'example' => [
                    'fixed'     => '101234567',
                    'mobile'    => '711234567',
                    'tollfree'  => '801234567',
                    'premium'   => '862345678',
                    'shared'    => '860123456',
                    'voip'      => '871234567',
                    'uan'       => '861123456',
                    'emergency' => '10111',
                ],
            ],
        ],
        'ZM' => [
            'code'     => '260',
            'patterns' => [
                'example' => [
                    'fixed'     => '211234567',
                    'mobile'    => '955123456',
                    'tollfree'  => '800123456',
                    'emergency' => '999',
                ],
            ],
        ],
        'ZW' => [
            'code'     => '263',
            'patterns' => [
                'example' => [
                    'fixed'     => '1312345',
                    'mobile'    => '711234567',
                    'voip'      => '8686123456',
                    'emergency' => '999',
                ],
            ],
        ],
    ];

    public function setUp()
    {
        $this->validator = new PhoneNumber();
    }

    /**
     * @dataProvider constructDataProvider
     *
     * @param array  $args
     * @param array  $options
     * @param string $locale
     */
    public function testConstruct(array $args, array $options, $locale = null)
    {
        if ($locale) {
            Locale::setDefault($locale);
        }

        $validator = new PhoneNumber($args);

        $this->assertSame($options['country'], $validator->getCountry());
    }

    public function constructDataProvider()
    {
        return [
            [
                [],
                ['country' => Locale::getRegion(Locale::getDefault())],
                null
            ],
            [
                [],
                ['country' => 'CN'],
                'zh_CN'
            ],
            [
                ['country' => 'CN'],
                ['country' => 'CN'],
                null
            ],
        ];
    }

    public function numbersDataProvider()
    {
        $data = [];
        foreach ($this->phone as $country => $parameters) {
            $countryRow = [
                'country'  => $country,
                'code'     => $parameters['code'],
                'patterns' => $parameters['patterns'],
            ];

            $data[] = $countryRow;
        }

        return $data;
    }

    /**
     * @dataProvider numbersDataProvider
     *
     * @param string $country
     * @param string $code
     * @param array $patterns
     */
    public function testExampleNumbers($country, $code, $patterns)
    {
        $this->validator->setCountry($country);
        foreach ($patterns['example'] as $type => $values) {
            $values = is_array($values) ? $values : [$values];
            foreach ($values as $value) {
                $this->validator->allowedTypes([$type]);
                $this->assertTrue($this->validator->isValid($value));

                // check with country code:
                $countryCodePrefixed = $code . $value;
                $this->assertTrue($this->validator->isValid($countryCodePrefixed));

                // check fully qualified E.123/E.164 international variants
                $fullyQualifiedDoubleO = '00' . $code . $value;
                $this->assertTrue($this->validator->isValid($fullyQualifiedDoubleO));

                $fullyQualifiedPlus = '+' . $code . $value;
                $this->assertTrue($this->validator->isValid($fullyQualifiedPlus));
            }
        }
    }

    /**
     * @dataProvider numbersDataProvider
     *
     * @param string $country
     * @param string $code
     * @param array $patterns
     */
    public function testExampleNumbersAgainstPossible($country, $code, $patterns)
    {
        $this->validator->allowPossible(true);
        $this->validator->setCountry($country);
        foreach ($patterns['example'] as $type => $values) {
            $values = is_array($values) ? $values : [$values];
            foreach ($values as $value) {
                $this->validator->allowedTypes([$type]);
                $this->assertTrue($this->validator->isValid($value));

                // check with country code:
                $countryCodePrefixed = $code . $value;
                $this->assertTrue($this->validator->isValid($countryCodePrefixed));

                // check fully qualified E.123/E.164 international variants
                $fullyQualifiedDoubleO = '00' . $code . $value;
                $this->assertTrue($this->validator->isValid($fullyQualifiedDoubleO), $fullyQualifiedDoubleO);

                $fullyQualifiedPlus = '+' . $code . $value;
                $this->assertTrue($this->validator->isValid($fullyQualifiedPlus), $fullyQualifiedPlus);
            }
        }
    }

    public function testAllowPossibleSetterGetter()
    {
        $this->assertFalse($this->validator->allowPossible());
        $this->validator->allowPossible(true);
        $this->assertTrue($this->validator->allowPossible());
    }

    public function testSetCountryMethodIsCaseInsensitive()
    {
        $this->validator->setCountry('us');
        $this->assertSame('US', $this->validator->getCountry());
    }

    /**
     * @dataProvider numbersDataProvider
     *
     * @param string $country
     * @param string $code
     * @param array $patterns
     */
    public function testInvalidTypes($country, $code, $patterns)
    {
        $this->validator->setCountry($country);
        if (!isset($patterns['invalid'])) {
            return;
        }
        foreach ($patterns['invalid'] as $type => $values) {
            $values = is_array($values) ? $values : [$values];
            foreach ($values as $value) {
                $this->validator->allowedTypes([$type]);
                $this->assertFalse($this->validator->isValid($value));

                // check with country code:
                $countryCodePrefixed = $code . $value;
                $this->assertFalse($this->validator->isValid($countryCodePrefixed));

                // check fully qualified E.123/E.164 international variants
                $fullyQualifiedDoubleO = '00' . $code . $value;
                $this->assertFalse($this->validator->isValid($fullyQualifiedDoubleO));

                $fullyQualifiedPlus = '+' . $code . $value;
                $this->assertFalse($this->validator->isValid($fullyQualifiedPlus));
            }
        }
    }
}

<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translator
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 25.Jul.2011
 */
return array(
    // Zend\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser float, string, ou inteiro",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' contém caracteres que não são alfabéticos e nem dígitos",
    "'%value%' is an empty string" => "'%value%' é uma string vazia",

    // Zend\Validator\Alpha
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "'%value%' contains non alphabetic characters" => "'%value%' contém caracteres não alfabéticos",
    "'%value%' is an empty string" => "'%value%' é uma string vazia",

    // Zend\Validator\Barcode
    "'%value%' failed checksum validation" => "'%value%' falhou na validação do checksum",
    "'%value%' contains invalid characters" => "'%value%' contém caracteres inválidos",
    "'%value%' should have a length of %length% characters" => "'%value%' tem um comprimento de %length% caracteres",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser string",

    // Zend\Validator\Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' não está entre '%min%' e '%max%', inclusivamente",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' não está exatamente entre '%min%' e '%max%'",

    // Zend\Validator\Callback
    "'%value%' is not valid" => "'%value%' não é válido",
    "An exception has been raised within the callback" => "Falha na chamada de retorno, exceção retornada",

    // Zend\Validator\Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' deve conter entre 13 e 19 dígitos",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "O algoritmo de Luhn (checksum de módulo 10) falhou em '%value%'",

    // Zend\Validator\CreditCard
    "'%value%' seems to contain an invalid checksum" => "'%value%' contém um checksum inválido",
    "'%value%' must contain only digits" => "'%value%' deve conter apenas dígitos",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "'%value%' contains an invalid amount of digits" => "'%value%' contém uma quantidade inválida de dígitos",
    "'%value%' is not from an allowed institute" => "'%value%' não vem de uma instituição autorizada",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' é um número de cartão de crédito inválido",
    "An exception has been raised while validating '%value%'" => "O serviço devolveu um erro enquanto validava '%value%'",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "O tipo especificado é inválido, o valor deve ser string, inteiro, matriz ou Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' não parece ser uma data válida",
    "'%value%' does not fit the date format '%format%'" => "'%value%' não se encaixa no formato de data '%format%'",

    // Zend\Validator\Db_Abstract
    "No record matching '%value%' was found" => "Não foram encontrados registros para '%value%'",
    "A record matching '%value%' was found" => "Um registro foi encontrado para '%value%'",

    // Zend\Validator\Digits
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser string, inteiro ou float",
    "'%value%' must contain only digits" => "'%value%' devem conter apenas dígitos",
    "'%value%' is an empty string" => "'%value%' é uma string vazia",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "'%value%' não é um endereço de e-mail válido no formato local-part@hostname",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "'%hostname%' não é um nome de host válido para o endereço de e-mail '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' não parece ter um registro MX válido para o endereço de e-mail '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network." => "'%hostname%' não é um segmento de rede roteável. O endereço de e-mail '%value%' não deve ser resolvido a partir de um rede pública.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' não corresponde com o formato dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' não corresponde com o formato quoted-string",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' não é uma parte local válida para o endereço de e-mail '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' excede o comprimento permitido",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Há muitos arquivos, são permitidos no máximo '%max%', mas '%count%' foram fornecidos",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Há poucos arquivos, são esperados no mínimo '%min%', mas '%count%' foram fornecidos",

    // Zend\Validator\File\Crc32
    "File '%value%' does not match the given crc32 hashes" => "O arquivo '%value%' não corresponde ao hash crc32 fornecido",
    "A crc32 hash could not be evaluated for the given file" => "Não foi possível avaliar um hash crc32 para o arquivo fornecido",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\ExcludeExtension
    "File '%value%' has a false extension" => "O arquivo '%value%' possui a extensão incorreta",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "O arquivo '%value%' tem o mimetype incorreto: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\Exists
    "File '%value%' does not exist" => "O arquivo '%value%' não existe",

    // Zend\Validator\File\Extension
    "File '%value%' has a false extension" => "O arquivo '%value%' possui a extensão incorreta",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Todos os arquivos devem ter um tamanho máximo de '%max%', mas um tamanho de '%size%' foi detectado",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Todos os arquivos devem ter um tamanho mínimo de '%min%', mas um tamanho de '%size%' foi detectado",
    "One or more files can not be read" => "Um ou mais arquivos não puderam ser lidos",

    // Zend\Validator\File\Hash
    "File '%value%' does not match the given hashes" => "O arquivo '%value%' não corresponde ao hash fornecido",
    "A hash could not be evaluated for the given file" => "Não foi possível avaliar um hash para o arquivo fornecido",
    "File '%value%' is not readable or does not exist"  => "O arquivo '%value%' não pode ser encontrado ou não existe",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "A largura máxima permitida para a imagem '%value%' deve ser '%maxwidth%', mas '%width%' foi detectada",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "A largura mínima esperada para a imagem '%value%' deve ser '%minwidth%', mas '%width%' foi detectada",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "A altura máxima permitida para a imagem '%value%' deve ser '%maxheight%', mas '%height%' foi detectada",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "A altura mínima esperada para a imagem '%value%' deve ser '%minheight%', mas '%height%' foi detectada",
    "The size of image '%value%' could not be detected" => "O tamanho da imagem '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "O arquivo '%value%' não está compactado: '%type%' detectado",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\IsImage
    "File '%value%' is no image, '%type%' detected" => "O arquivo '%value%' não é uma imagem: '%type%' detectado",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\Md5
    "File '%value%' does not match the given md5 hashes" => "O arquivo '%value%' não corresponde ao hash md5 fornecido",
    "A md5 hash could not be evaluated for the given file" => "Não foi possível avaliar um hash md5 para o arquivo fornecido",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\MimeType
    "File '%value%' has a false mimetype of '%type%'" => "O arquivo '%value%' tem o mimetype incorreto: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\NotExists
    "File '%value%' exists" => "O arquivo '%value%' existe",

    // Zend\Validator\File\Sha1
    "File '%value%' does not match the given sha1 hashes" => "O arquivo '%value%' não corresponde ao hash sha1 fornecido",
    "A sha1 hash could not be evaluated for the given file" => "Não foi possível avaliar um hash sha1 para o arquivo fornecido",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "O tamanho máximo permitido para o arquivo '%value%' é '%max%', mas '%size%' foram detectados",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "O tamanho mínimo esperado para o arquivo '%value%' é '%min%', mas '%size%' foram detectados",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "O arquivo '%value%' excede o tamanho definido na configuração",
    "File '%value%' exceeds the defined form size" => "O arquivo '%value%' excede o tamanho definido do formulário",
    "File '%value%' was only partially uploaded" => "O arquivo '%value%' foi apenas parcialmente enviado",
    "File '%value%' was not uploaded" => "O arquivo '%value%' não foi enviado",
    "No temporary directory was found for file '%value%'" => "Nenhum diretório temporário foi encontrado para o arquivo '%value%'",
    "File '%value%' can't be written" => "O arquivo '%value%' não pôde ser escrito",
    "A PHP extension returned an error while uploading the file '%value%'" => "Uma extensão do PHP retornou um erro enquanto o arquivo '%value%' era enviado",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "O arquivo '%value%' foi enviado ilegalmente. Este poderia ser um possível ataque",
    "File '%value%' was not found" => "O arquivo '%value%' não foi encontrado",
    "Unknown error while uploading file '%value%'" => "Erro desconhecido ao enviar o arquivo '%value%'",

    // Zend\Validator\File\WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Há muitas palavras, são permitidas no máximo '%max%', mas '%count%' foram contadas",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "Há poucas palavras, são esperadas no mínimo '%min%', mas '%count%' foram contadas",
    "File '%value%' is not readable or does not exist" => "O arquivo '%value%' não pode ser lido ou não existe",

    // Zend\Validator\Float
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser float, string, ou inteiro",
    "'%value%' does not appear to be a float" => "'%value%' não parece ser um float",

    // Zend\Validator\GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' não é maior que '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' não contém somente caracteres hexadecimais",

    // Zend\Validator\Hostname
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' parece ser um endereço de IP, mas endereços de IP não são permitidos",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' parece ser um hostname de DNS, mas o TLD não corresponde a nenhum TLD conhecido",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' parece ser um hostname de DNS, mas contém um traço em uma posição inválida",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' parece ser um hostname de DNS, mas não corresponde ao esquema de hostname para o TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' parece ser um hostname de DNS, mas o TLD não pôde ser extraído",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' não corresponde com a estrutura esperada para um hostname de DNS",
    "'%value%' does not appear to be a valid local network name" => "'%value%' não parece ser um nome de rede local válido",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' parece ser um nome de rede local, mas os nomes de rede local não são permitidos",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' parece ser um hostname de DNS, mas a notação punycode fornecida não pode ser decodificada",
    "'%value%' does not appear to be a valid URI hostname" => "'%value%' não parece ser um URI hostname válido",

    // Zend\Validator\Iban
    "Unknown country within the IBAN '%value%'" => "País desconhecido para o IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' não é um formato IBAN válido",
    "'%value%' has failed the IBAN check" => "'%value%' falhou na verificação do IBAN",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "Os dois tokens fornecidos não combinam",
    "No token was provided to match against" => "Nenhum token foi fornecido para a comparação",

    // Zend\Validator\InArray
    "'%value%' was not found in the haystack" => "'%value%' não faz parte dos valores esperados",

    // Zend\Validator\Int
    "Invalid type given. String or integer expected" => "O tipo especificado é inválido, o valor deve ser string ou inteiro",
    "'%value%' does not appear to be an integer" => "'%value%' não parece ser um número inteiro",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "'%value%' does not appear to be a valid IP address" => "'%value%' não parece ser um endereço de IP válido",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "O tipo especificado é inválido, o valor deve ser string ou inteiro",
    "'%value%' is not a valid ISBN number" => "'%value%' não é um número ISBN válido",

    // Zend\Validator\LessThan
    "'%value%' is not less than '%max%'" => "'%value%' não é menor que '%max%'",

    // Zend\Validator\NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "O tipo especificado é inválido, o valor deve ser float, string, matriz, booleano ou inteiro",
    "Value is required and can't be empty" => "O valor é obrigatório e não pode estar vazio",

    // Zend\Validator\PostCode
    "Invalid type given. String or integer expected" => "O tipo especificado é inválido. O valor deve ser uma string ou um inteiro",
    "'%value%' does not appear to be a postal code" => "'%value%' não parece ser um código postal",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "O tipo especificado é inválido, o valor deve ser string, inteiro ou float",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' não corresponde ao padrão '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "Houve um erro interno durante o uso do padrão '%pattern%'",

    // Zend\Validator\Sitemap\Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' não é um changefreq de sitemap válido",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",

    // Zend\Validator\Sitemap\Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' não é um lastmod de sitemap válido",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",

    // Zend\Validator\Sitemap\Loc
    "'%value%' is not a valid sitemap location" => "'%value%' não é uma localização de sitemap válida",
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",

    // Zend\Validator\Sitemap\Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' não é uma prioridade de sitemap válida",
    "Invalid type given. Numeric string, integer or float expected" => "O tipo especificado é inválido, o valor deve ser um inteiro, um float ou uma string numérica",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "O tipo especificado é inválido, o valor deve ser uma string",
    "'%value%' is less than %min% characters long" => "O tamanho de '%value%' é inferior a %min% caracteres",
    "'%value%' is more than %max% characters long" => "O tamanho de '%value%' é superior a %max% caracteres",
);

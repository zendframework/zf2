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
 * @package    Zend_I18n_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Translator\Plural;

use stdClass;

/**
 * Plural rule parser.
 * 
 * This plural rule parser is implemented after the article "Top Down Operator
 * Precedence" described in <http://javascript.crockford.com/tdop/tdop.html>.
 *
 * @package    Zend_I18n_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Parser
{
    /**
     * String to parse.
     *
     * @var string
     */
    protected $string;

    /**
     * Current lexer position in the string.
     *
     * @var integer
     */
    protected $currentPos;
    
    /**
     * Current token.
     * 
     * @var Symbol
     */
    protected $currentToken;
    
    /**
     * Table of symbols.
     * 
     * @var array
     */
    protected $symbolTable = array();

    /**
     * Create a new plural parser.
     * 
     * @return void 
     */
    public function __construct()
    {
        $this->populateSymbolTable();
    }
    
    /**
     * Populate the symbol table.
     * 
     * @return void 
     */
    protected function populateSymbolTable()
    {       
        // Ternary operators
        $this->registerSymbol('?', 20)->setLeftDenotationGetter(
            function (Symbol $self, Symbol $left) {
                $self->first  = $left;
                $self->second = $self->parser->expression();
                $self->parser->advance(':');
                $self->third  = $self->parser->expression();
                return $self;
            }
        );
        $this->registerSymbol(':');

        // Boolean operators
        $this->registerLeftInfixSymbol('||', 30);
        $this->registerLeftInfixSymbol('&&', 40);
        
        // Equal operators
        $this->registerLeftInfixSymbol('==', 50);
        $this->registerLeftInfixSymbol('!=', 50);
        
        // Compare operators
        $this->registerLeftInfixSymbol('>', 50);
        $this->registerLeftInfixSymbol('<', 50);
        $this->registerLeftInfixSymbol('>=', 50);
        $this->registerLeftInfixSymbol('<=', 50);
        
        // Add operators
        $this->registerLeftInfixSymbol('-', 60);
        $this->registerLeftInfixSymbol('+', 60);
        
        // Multiply operators
        $this->registerLeftInfixSymbol('*', 70);
        $this->registerLeftInfixSymbol('/', 70);
        $this->registerLeftInfixSymbol('%', 70);
        
        // Not operator
        $this->registerPrefixSymbol('!', 80);
        
        // Literals
        $this->registerSymbol('n')->setNullDenotationGetter(
            function (Symbol $self) {
                return $self;
            }
        );
        $this->registerSymbol('number')->setNullDenotationGetter(
            function (Symbol $self) {
                return $self;
            }
        );
        
        // Parentheses
        $this->registerSymbol('(')->setNullDenotationGetter(
            function (Symbol $self) {
                $expression = $self->parser->expression();
                $self->parser->advance(')');
                return $expression;
            }
        );
        $this->registerSymbol(')');
        
        // Eof
        $this->registerSymbol('eof');
    }
    
    /**
     * Register a left infix symbol.
     * 
     * @param  string  $id
     * @param  integer $leftBindingPower 
     * @return void
     */
    protected function registerLeftInfixSymbol($id, $leftBindingPower)
    {
        $this->registerSymbol($id, $leftBindingPower)->setLeftDenotationGetter(
            function (Symbol $self, Symbol $left) use ($leftBindingPower) {
                $self->first  = $left;
                $self->second = $self->parser->expression($leftBindingPower);
                return $self;
            }
        );
    }
    
    /**
     * Register a right infix symbol.
     * 
     * @param  string  $id
     * @param  integer $leftBindingPower 
     * @return void
     */
    protected function registerRightInfixSymbol($id, $leftBindingPower)
    {
        $this->registerSymbol($id, $leftBindingPower)->setLeftDenotationGetter(
            function (Symbol $self, Symbol $left) use ($leftBindingPower) {
                $self->first  = $left;
                $self->second = $self->parser->expression($leftBindingPower - 1);
                return $self;
            }
        );
    }
    
    /**
     * Register a prefix symbol.
     * 
     * @param  string  $id
     * @param  integer $leftBindingPower 
     * @return void
     */
    protected function registerPrefixSymbol($id, $leftBindingPower)
    {
        $this->registerSymbol($id, $leftBindingPower)->setNullDenotationGetter(
            function (Symbol $self) use ($leftBindingPower) {
                $self->first  = $self->parser->expression($leftBindingPower);
                $self->second = null;
                return $self;
            }
        );
    }
    
    /**
     * Register a symbold.
     * 
     * @param  string  $id
     * @param  integer $leftBindingPower
     * @return Symbol
     */
    protected function registerSymbol($id, $leftBindingPower = 0)
    {
        if (isset($this->symbolTable[$id])) {
            $symbol = $this->symbolTable[$id];
            $symbol->leftBindingPower = max(
                $symbol->leftBindingPower,
                $leftBindingPower
            );
        } else {
            $symbol = new Symbol($this, $id, $leftBindingPower);
            $this->symbolTable[$id] = $symbol;
        }
        
        return $symbol;
    }
    
    /**
     * Get a new symbol.
     * 
     * @param string $id 
     */
    protected function getSymbol($id)
    {
        if (!isset($this->symbolTable[$id])) {
            // Unkown symbol exception
        }
        
        return clone $this->symbolTable[$id];
    }
    
    /**
     * Parse a string.
     *
     * @param  string $string
     * @return array
     */
    public function parse($string)
    {
        $this->string       = $string . "\0";
        $this->currentPos   = 0;
        $this->currentToken = $this->getNextToken();
        
        return $this->expression();
    }

    /**
     * Parse an expression.
     * 
     * @param  integer $rightBindingPower
     * @return Symbol
     */
    public function expression($rightBindingPower = 0)
    {
        $token              = $this->currentToken;
        $this->currentToken = $this->getNextToken();
        $left               = $token->getNullDenotation();
        
        while ($rightBindingPower < $this->currentToken->leftBindingPower) {
            $token              = $this->currentToken;
            $this->currentToken = $this->getNextToken();
            $left               = $token->getLeftDenoation($left);
        }
        
        return $left;
    }
    
    /**
     * Advance the current token and optionally check the old token id.
     * 
     * @param  string $id
     * @return void
     */
    public function advance($id = null)
    {
        if ($id !== null && $this->currentToken->id !== $id) {
            exit('Expected ' . $id);
        }
        
        $this->currentToken = $this->getNextToken();
    }

    /**
     * Get the next token.
     *
     * @return array
     */
    protected function getNextToken()
    {
        $token = array();

        while ($this->string[$this->currentPos] === ' ' || $this->string[$this->currentPos] === "\t") {
            $this->currentPos++;
        }

        $result = $this->string[$this->currentPos++];
        $value  = null;

        switch ($result) {
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
                while (ctype_digit($this->string[$this->currentPos])) {
                    $result .= $this->string[$this->currentPos++];
                }

                $id    = 'number';
                $value = (int) $result;
                break;
                
            case '=':
            case '&':
            case '|':
                if ($this->string[$this->currentPos + 1] === $result) {
                    $this->currentPos++;
                    $id = $result . $result;
                } else {
                    // Yield error
                }
                break;

            case '!':
            case '<':
            case '>':
                if ($this->string[$this->currentPos + 1] === '=') {
                    $this->currentPos++;
                    $result .= '=';
                }

                $id = $result;
                break;

            case '*':
            case '/':
            case '%':
            case '+':
            case '-':
            case 'n':
            case '?':
            case ':':
            case '(':
            case ')':
                $id = $result;
                break;

            case ';':
            case "\n":
            case "\0":
                $id = 'eof';
                $this->currentPos--;
                break;

            default:
                exit('parse error: ' . $result);
                // Yield error
                break;
        }

        $token = $this->getSymbol($id);
        $token->value = $value;
        
        return $token;
    }
} 
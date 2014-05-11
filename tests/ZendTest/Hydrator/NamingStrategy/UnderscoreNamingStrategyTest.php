<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZendTest\Hydrator\NamingStrategy;

use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

class UnderscoreNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function extractionProvider()
    {
        return [
            [
                'value'  => 'firstName',
                'result' => 'first_name'
            ],
            [
                'value'  => 'FirstName',
                'result' => 'first_name'
            ],
            [
                'value'  => 'city',
                'result' => 'city'
            ],
            [
                'value'  => 'City',
                'result' => 'city'
            ]
        ];
    }

    /**
     * @dataProvider extractionProvider
     */
    public function testCanTransformNamesForExtraction($value, $result)
    {
        $namingStrategy = new UnderscoreNamingStrategy();
        $this->assertEquals($result, $namingStrategy->getNameForExtraction($value));
    }

    public function hydrationProvider()
    {
        return [
            [
                'value'  => 'first_name',
                'result' => 'firstName'
            ],
            [
                'value'  => 'First_Name',
                'result' => 'firstName'
            ],
            [
                'value'  => 'City',
                'result' => 'city'
            ]
        ];
    }

    /**
     * @dataProvider hydrationProvider
     */
    public function testCanTransformNamesForHydration($value, $result)
    {
        $namingStrategy = new UnderscoreNamingStrategy();
        $this->assertEquals($result, $namingStrategy->getNameForHydration($value));
    }
}

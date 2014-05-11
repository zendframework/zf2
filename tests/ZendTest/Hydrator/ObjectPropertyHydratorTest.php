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

namespace ZendTest\Hydrator;

use Zend\Hydrator\ObjectPropertyHydrator;
use ZendTest\Hydrator\Asset\ObjectProperty;

class ObjectPropertyHydratorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtraction()
    {
        $object = new ObjectProperty();
        $object->firstName = 'Marco';
        $object->lastName  = 'Pivetta';

        $hydrator = new ObjectPropertyHydrator();

        $expectedData = ['first_name' => 'Marco', 'last_name' => 'Pivetta'];
        $data         = $hydrator->extract($object);

        $this->assertEquals($expectedData, $data);
    }

    public function testHydration()
    {
        $object   = new ObjectProperty();
        $hydrator = new ObjectPropertyHydrator();

        $result = $hydrator->hydrate(['first_name' => 'Manuel', 'last_name' => 'Stosic'], $object);

        $this->assertSame($object, $result);
        $this->assertEquals('Manuel', $object->firstName);
        $this->assertEquals('Stosic', $object->lastName);
    }
}

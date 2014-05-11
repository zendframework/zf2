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

use Zend\Hydrator\ArraySerializableHydrator;
use ZendTest\Hydrator\Asset\ArraySerializable;

class ArraySerializableHydratorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtraction()
    {
        $object = new ArraySerializable();
        $object->setFirstName('Matthew');
        $object->setLastName('Weier O\'Phinney');

        $hydrator = new ArraySerializableHydrator();

        $expectedData = ['first_name' => 'Matthew', 'last_name' => 'Weier O\'Phinney'];
        $data         = $hydrator->extract($object);

        $this->assertEquals($expectedData, $data);
    }

    public function testHydration()
    {
        $object   = new ArraySerializable();
        $hydrator = new ArraySerializableHydrator();

        $result = $hydrator->hydrate(['first_name' => 'Ralph', 'last_name' => 'Schindler'], $object);

        $this->assertSame($object, $result);
        $this->assertEquals('Ralph', $object->getFirstName());
        $this->assertEquals('Schindler', $object->getLastName());
    }
}

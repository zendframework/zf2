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

namespace ZendTest\Hydrator\Filter;

use Zend\Hydrator\Context\ExtractionContext;
use Zend\Hydrator\Filter\NumberOfParametersFilter;
use ZendTest\Hydrator\Asset\NumberOfParametersObject;

class NumberOfParametersFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $object  = new NumberOfParametersObject();
        $context = new ExtractionContext($object);

        // Test for 0 parameters
        $filter = new NumberOfParametersFilter();
        $this->assertTrue($filter->accept('methodWithNoParameters', $context));
        $this->assertTrue($filter->accept('Object::methodWithNoParameters', $context));
        $this->assertFalse($filter->accept('methodWithOneParameter', $context));
        $this->assertFalse($filter->accept('Object::methodWithOneParameter', $context));

        // Test for 1 parameter
        $filter = new NumberOfParametersFilter(1);
        $this->assertFalse($filter->accept('methodWithNoParameters', $context));
        $this->assertFalse($filter->accept('Object::methodWithNoParameters', $context));
        $this->assertTrue($filter->accept('methodWithOneParameter', $context));
        $this->assertTrue($filter->accept('Object::methodWithOneParameter', $context));
    }

    public function testThrowExceptionForUnknownMethod()
    {
        $this->setExpectedException(
            'Zend\Hydrator\Exception\InvalidArgumentException',
            'Method "unknownMethod" does not exist'
        );

        $object  = new NumberOfParametersObject();
        $context = new ExtractionContext($object);
        $filter  = new NumberOfParametersFilter();

        $filter->accept('unknownMethod', $context);
    }
}

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

namespace ZendTest\Hydrator\Strategy;

use DateTime;
use Zend\Hydrator\Strategy\DateStrategy;

class DateStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testSettersAndGetters()
    {
        $strategy = new DateStrategy();
        $strategy->setFormat(DateTime::ISO8601);

        $this->assertEquals(DateTime::ISO8601, $strategy->getFormat());
    }

    public function testAssertDefaultFormatIsRFC3339()
    {
        $strategy = new DateStrategy();
        $this->assertEquals(DateTime::RFC3339, $strategy->getFormat());
    }

    public function testExtractDateUsingDefaultFormat()
    {
        $dateTime = new DateTime('2014-05-06T00:00:00+02:00');

        $strategy = new DateStrategy();
        $date     = $strategy->extract($dateTime);

        $this->assertEquals('2014-05-06T00:00:00+02:00', $date);
    }

    public function testExtractDateUsingCustomFormat()
    {
        $dateTime = new DateTime('2014-05-06T00:00:00+02:00');

        $strategy = new DateStrategy('Y-m-d');
        $date     = $strategy->extract($dateTime);

        $this->assertEquals('2014-05-06', $date);
    }

    public function testCanExtractIfNotDateTime()
    {
        $strategy = new DateStrategy();
        $date     = $strategy->extract(new \stdClass);

        $this->assertInstanceOf('stdClass', $date);
    }

    public function testHydrateDate()
    {
        $strategy = new DateStrategy();
        $date     = $strategy->hydrate('2014-05-06');

        $this->assertInstanceOf('DateTime', $date);
        $this->assertEquals('2014-05-06', $date->format('Y-m-d'));
    }
}

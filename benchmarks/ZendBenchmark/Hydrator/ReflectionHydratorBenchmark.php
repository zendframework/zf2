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

namespace ZendBenchmark\Hydrator;

use Athletic\AthleticEvent;
use Zend\Hydrator\ReflectionHydrator;
use ZendBenchmark\Hydrator\Asset\ClassMethodsObject;

class ReflectionHydratorBenchmark extends AthleticEvent
{
    /**
     * @var ReflectionHydrator
     */
    protected $hydrator;

    public function __construct()
    {
        $this->hydrator = new ReflectionHydrator();
    }

    /**
     * @iterations 100
     */
    public function hydratorExtractionWithTwentyProperties()
    {
        $object = new ClassMethodsObject();
        $this->hydrator->extract($object);
    }

    /**
     * @iterations 100
     */
    public function hydratorExtractionReusingHydratorWithTwentyProperties()
    {
        for ($i = 0 ; $i != 20 ; ++$i) {
            $object = new ClassMethodsObject();
            $this->hydrator->extract($object);
        }
    }

    /**
     * @iterations 100
     */
    public function hydratorHydrationWithTwentyProperties()
    {
        $object = new ClassMethodsObject();
        $data   = [
            'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
            'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
            'eleven' => 11, 'twelve' => 12, 'thirteen' => 13, 'fourteen' => 14, 'fifteen' => 15,
            'sixteen' => 16, 'seventeen' => 17, 'eighteen' => 18, 'nineteen' => 19, 'twenty' => 19
        ];

        $this->hydrator->hydrate($data, $object);
    }

    /**
     * @iterations 100
     */
    public function hydratorHydrationReusingHydratorWithTwentyProperties()
    {
        for ($i = 0 ; $i != 20 ; ++$i) {
            $object = new ClassMethodsObject();
            $data   = [
                'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
                'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
                'eleven' => 11, 'twelve' => 12, 'thirteen' => 13, 'fourteen' => 14, 'fifteen' => 15,
                'sixteen' => 16, 'seventeen' => 17, 'eighteen' => 18, 'nineteen' => 19, 'twenty' => 19
            ];

            $this->hydrator->hydrate($data, $object);
        }
    }
}

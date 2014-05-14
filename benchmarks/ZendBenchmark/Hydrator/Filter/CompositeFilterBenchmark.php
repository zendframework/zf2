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

namespace ZendBenchmark\Hydrator\Filter;

use Athletic\AthleticEvent;
use Zend\Hydrator\Filter\CompositeFilter;
use Zend\Hydrator\Filter\HasFilter;
use Zend\Hydrator\Filter\IsFilter;
use Zend\Hydrator\Filter\GetFilter;
use Zend\Hydrator\Filter\OptionalParametersFilter;
use ZendBenchmark\Hydrator\Asset\CompositeFilterObject;

class CompositeFilterBenchmark extends AthleticEvent
{
    /**
     * @var CompositeFilter
     */
    protected $filter;

    /**
     * @var CompositeFilterObject
     */
    protected $object;

    public function classSetUp()
    {
        $this->filter = new CompositeFilter([], CompositeFilter::CONDITION_AND);
        $this->object = new CompositeFilterObject();

        $this->filter->addFilter(new CompositeFilter([
            new GetFilter(), new HasFilter(), new IsFilter()
        ]));
        $this->filter->addFilter(new OptionalParametersFilter());
    }

    /**
     * @iterations 1000
     */
    public function traverseCompositeFilter()
    {
        $this->filter->accept('getOne', $this->object);
        $this->filter->accept('getTwo', $this->object);
        $this->filter->accept('isThree', $this->object);
    }
}

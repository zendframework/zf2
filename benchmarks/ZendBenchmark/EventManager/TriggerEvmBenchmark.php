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

namespace ZendBenchmark\EventManager;

use Athletic\AthleticEvent;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;

class TriggerEvmBenchmark extends AthleticEvent
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    public function classSetUp()
    {
        $this->eventManager = new EventManager();

        // Add one hundred listeners to two different events with random priorities
        for ($i = 0 ; $i != 100 ; ++$i) {
            $this->eventManager->attach('eventName1', function() {}, rand(0, 100));
            $this->eventManager->attach('eventName2', function() {}, rand(0, 100));
        }

        // Attach also fifty listeners to the wildcard
        for ($i = 0 ; $i != 10 ; ++$i) {
            $this->eventManager->attach('*', function() {}, rand(0, 100));
        }
    }

    /**
     * @iterations 50
     */
    public function triggerEventOneTimeWithoutSharedManager()
    {
        $this->eventManager->trigger('eventName1');
    }

    /**
     * @iterations 50
     */
    public function triggerEventOneTimeWithEmptySharedManager()
    {
        $this->eventManager->setSharedManager(new SharedEventManager());
        $this->eventManager->trigger('eventName1');
    }

    /**
     * @iterations 50
     */
    public function triggerEventTenTimesWithoutSharedManager()
    {
        for ($i = 0 ; $i !== 10 ; ++$i) {
            $this->eventManager->trigger('eventName1');
        }
    }

    /**
     * @iterations 50
     */
    public function triggerEventTenTimesWithEmptySharedManager()
    {
        $this->eventManager->setSharedManager(new SharedEventManager());

        for ($i = 0 ; $i !== 10 ; ++$i) {
            $this->eventManager->trigger('eventName1');
        }
    }
}

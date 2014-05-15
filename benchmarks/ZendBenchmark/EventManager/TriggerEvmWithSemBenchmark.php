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

class TriggerEvmWithSemBenchmark extends AthleticEvent
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var SharedEventManager
     */
    protected $sharedManager;

    public function classSetUp()
    {
        $this->eventManager  = new EventManager();
        $this->sharedManager = new SharedEventManager();

        $this->eventManager->setIdentifiers(['identifier1', 'identifier2']);
        $this->eventManager->setSharedManager($this->sharedManager);

        // Add fifty listeners to two different events with random priorities in the EVM and SEM
        for ($i = 0 ; $i != 50 ; ++$i) {
            $this->eventManager->attach('eventName1', function() {}, rand(0, 100));
            $this->eventManager->attach('eventName2', function() {}, rand(0, 100));

            $this->sharedManager->attach('identifier1', 'eventName1', function() {}, rand(0, 100));
            $this->sharedManager->attach('identifier2', 'eventName1', function() {}, rand(0, 100));
            $this->sharedManager->attach('identifier3', 'eventName1', function() {}, rand(0, 100));
        }

        // Attach also fifty listeners to the wildcard
        for ($i = 0 ; $i != 10 ; ++$i) {
            $this->eventManager->attach('*', function() {}, rand(0, 100));
            $this->sharedManager->attach('*', 'eventName1', function() {}, rand(0, 100));
            $this->sharedManager->attach('identifier1', '*', function() {}, rand(0, 100));
            $this->sharedManager->attach('*', '*', function() {}, rand(0, 100));
        }
    }

    /**
     * @iterations 50
     */
    public function triggerEventOneTime()
    {
        $this->eventManager->trigger('eventName1');
    }

    /**
     * @iterations 50
     */
    public function triggerEventTenTimes()
    {
        for ($i = 0 ; $i !== 10 ; ++$i) {
            $this->eventManager->trigger('eventName1');
        }
    }
}

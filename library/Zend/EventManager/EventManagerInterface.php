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

namespace Zend\EventManager;

/**
 * EventManagerInterface
 */
interface EventManagerInterface 
{
    /**
     * Attach a listener for an event name, at a given priority
     *
     * @param  string         $eventName
     * @param  callable|array $callbackOrSpec
     * @param  int            $priority
     * @return void
     */
    public function attach($eventName, $callbackOrSpec, $priority = 1);

    /**
     * Detach a listener for an event name (or all listeners if no $callbackOrSpec is passed)
     *
     * @param  string $eventName
     * @param  mixed  $callbackOrSpec
     * @return bool True if was detached, false otherwise
     */
    public function detach($eventName, $callbackOrSpec = null);

    /**
     * Trigger an event
     *
     * @param  string         $eventName
     * @param  EventInterface $event
     * @return ResponseCollection
     */
    public function trigger($eventName, EventInterface $event);

    /**
     * @param  string         $eventName
     * @param  EventInterface $event
     * @param  callable       $callback
     * @return ResponseCollection
     */
    public function triggerUntil($eventName, EventInterface $event, callable $callback);
}
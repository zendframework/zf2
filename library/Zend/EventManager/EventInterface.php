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
 * EventInterface
 */
interface EventInterface
{
    /**
     * Set a list of params
     *
     * @param  array $params
     * @return void
     */
    public function setParams(array $params);

    /**
     * Get a list of params
     *
     * @return array
     */
    public function getParams();

    /**
     * Set a single param
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function setParam($key, $value);

    /**
     * Get a single param
     *
     * @param  string $key
     * @param  mixed  $defaultValue
     * @return mixed
     */
    public function getParam($key, $defaultValue = null);

    /**
     * Stop the propagation of the event
     *
     * @return void
     */
    public function stopPropagation();

    /**
     * Is the propagation stopped?
     *
     * @return bool
     */
    public function isPropagationStopped();
}
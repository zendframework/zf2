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
 * Base Event
 */
class Event implements EventInterface
{
    /**
     * @var bool
     */
    protected $stopPropagation = false;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * {@inheritDoc}
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * {@inheritDoc}
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getParam($key, $defaultValue = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function stopPropagation()
    {
        $this->stopPropagation = true;
    }

    /**
     * {@inheritDoc}
     */
    public function isPropagationStopped()
    {
        return $this->stopPropagation;
    }
}
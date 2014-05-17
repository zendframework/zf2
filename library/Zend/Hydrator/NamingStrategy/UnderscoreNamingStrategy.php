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

namespace Zend\Hydrator\NamingStrategy;
use Zend\Hydrator\Context\ExtractionContext;
use Zend\Hydrator\Context\HydrationContext;

/**
 * This strategy assumes that incoming data is underscore_separated, and transforms the names
 * to camelCased. This is often used for API, where the convention is to used underscore_separated
 * property keys.
 *
 * This is a very sensitive performance class, so we need optimized string methods instead of
 * Zend filters. Do not change that unless you know what you're doing!
 */
class UnderscoreNamingStrategy implements NamingStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function getNameForExtraction($name, ExtractionContext $context = null)
    {
        return strtolower(preg_replace('/\B([A-Z])/', '_$0', $name));
    }

    /**
     * {@inheritDoc}
     */
    public function getNameForHydration($name, HydrationContext $context = null)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($name)))));
    }
}

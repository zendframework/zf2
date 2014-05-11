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

namespace ZendBenchmark\Hydrator\Asset;

class ArraySerializableObject
{
    protected $one       = 'one';
    protected $two       = 'two';
    protected $three     = 'three';
    protected $four      = 'four';
    protected $five      = 'five';
    protected $six       = 'six';
    protected $seven     = 'seven';
    protected $eight     = 'eight';
    protected $nine      = 'nine';
    protected $ten       = 'ten';
    protected $eleven    = 'eleven';
    protected $twelve    = 'twelve';
    protected $thirteen  = 'thirteen';
    protected $fourteen  = 'fourteen';
    protected $fifteen   = 'fifteen';
    protected $sixteen   = 'sixteen';
    protected $seventeen = 'seventeen';
    protected $eighteen  = 'eighteen';
    protected $nineteen  = 'nineteen';
    protected $twenty    = 'twenty';

    public function getArrayCopy()
    {
        return [
            'one' => $this->one, 'two' => $this->two, 'three' => $this->three, 'four' => $this->four,
            'five' => $this->five, 'six' => $this->six, 'seven' => $this->seven, 'eight' => $this->eight,
            'nine' => $this->nine, 'ten' => $this->ten, 'eleven' => $this->eleven, 'twelve' => $this->twelve,
            'thirteen' => $this->thirteen, 'fourteen' => $this->fourteen, 'fifteen' => $this->fifteen,
            'sixteen' => $this->sixteen, 'seventeen' => $this->seventeen, 'eighteen' => $this->eighteen,
            'nineteen' => $this->nineteen, 'twenty' => $this->twenty
        ];
    }

    public function populate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}

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

namespace InputFilter;

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

class InputTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertDefaults()
    {
        $input = new Input();

        $this->assertNull($input->getName());
        $this->assertFalse($input->isRequired());
        $this->assertFalse($input->allowEmpty());
        $this->assertFalse($input->breakOnFailure());
    }

    public function testSettersAndGetters()
    {
        $input = new Input();

        $input->setName('foo');
        $input->setRequired(true);
        $input->setAllowEmpty(true);
        $input->setBreakOnFailure(true);

        $this->assertEquals('foo', $input->getName());
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->allowEmpty());
        $this->assertTrue($input->breakOnFailure());
    }

    public function testCanLazyLoadValidatorChain()
    {
        $input = new Input();
        $this->assertInstanceOf(ValidatorChain::class, $input->getValidatorChain());
    }

    public function testCanLazyLoadFilterChain()
    {
        $input = new Input();
        $this->assertInstanceOf(FilterChain::class, $input->getFilterChain());
    }
}

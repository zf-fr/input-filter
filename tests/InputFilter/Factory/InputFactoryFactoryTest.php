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

namespace InputFilterTest\Factory;

use InputFilter\Factory\InputFactoryFactory;
use InputFilter\InputFactory;
use InputFilter\InputFilterPluginManager;
use Zend\Filter\FilterPluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\ValidatorPluginManager;

class InputFactoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceLocator = new ServiceManager();

        $serviceLocator->setService(
            InputFilterPluginManager::class,
            $this->getMock(InputFilterPluginManager::class, [], [], '', false)
        );

        $serviceLocator->setService(
            'ValidatorManager',
            $this->getMock(ValidatorPluginManager::class, [], [], '', false)
        );

        $serviceLocator->setService(
            'FilterManager',
            $this->getMock(FilterPluginManager::class, [], [], '', false)
        );

        $factory = new InputFactoryFactory();
        $object  = $factory->createService($serviceLocator);

        $this->assertInstanceOf(InputFactory::class, $object);
    }
}

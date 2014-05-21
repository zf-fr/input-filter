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

use InputFilter\Factory\InputFilterPluginManagerFactory;
use InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class InputFilterPluginManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $config = [
            'zfr_input_filters' => []
        ];

        $serviceLocator = $this->getMock(ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())->method('get')->with('Config')->will($this->returnValue($config));

        $factory = new InputFilterPluginManagerFactory();
        $object  = $factory->createService($serviceLocator);

        $this->assertInstanceOf(InputFilterPluginManager::class, $object);
        $this->assertSame($serviceLocator, $object->getServiceLocator());
    }
}

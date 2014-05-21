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

namespace InputFilterTest\ValidationGroup;

use InputFilter\ValidationGroup\ValidationGroupFilter;

class ValidationGroupFilterTest extends \PHPUnit_Framework_TestCase
{
    public function dataProvider()
    {
        return [
            [
                'values'           => ['first_name', 'last_name', 'email'],
                'validation_group' => ['first_name', 'last_name'],
                'expected'         => ['first_name', 'last_name']
            ],

            [
                'values'           => ['first_name'],
                'validation_group' => ['first_name', 'last_name'],
                'expected'         => ['first_name']
            ],

            [
                'values'           => ['first_name', 'address' => ['city']],
                'validation_group' => ['first_name', 'address'],
                'expected'         => ['first_name', ['city']]
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testValidationGroupFilter(array $values, array $validationGroup, array $expected)
    {
        $iterator = new \ArrayIterator($values);
        $filter   = new ValidationGroupFilter($iterator, $validationGroup);

        $result = [];

        foreach ($filter as $element) {
            $result[] = $element;
        }

        $this->assertEquals($expected, $result);
    }
}

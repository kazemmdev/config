<?php

use Kazemmdev\Config\ConfigRepository;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertTrue;

beforeEach(closure: function () {
    $this->defaultConfig = [
        'env' => 'testing',
        'name' => 'ConfigRepository'
    ];
    $this->buildInstance = ConfigRepository::build($this->defaultConfig);
});

it('can be built', function () {
    assertInstanceOf(
        expected: ConfigRepository::class,
        actual: $this->buildInstance
    );
});

it('can check if configuration has item', function () {
    assertTrue(
        condition: $this->buildInstance->has('env')
    );
    assertFalse(
        condition: $this->buildInstance->has('version')
    );
});

it('can get a value from a configuration', function () {
    assertEquals(
        expected: 'testing',
        actual: $this->buildInstance->get('env')
    );
    assertEquals(
        expected: 'ConfigRepository',
        actual: $this->buildInstance->get('name')
    );
});

it('can get many values from a configuration', function () {
    assertIsArray(
        actual: $this->buildInstance->getMany(['env', 'name'])
    );
    assertArrayHasKey(
        key: 'env',
        array: $this->buildInstance->getMany(['env', 'name'])
    );
});

it('can set a new value into a configuration', function () {
    $this->assertEquals(
        $this->defaultConfig,
        $this->buildInstance->all()
    );
    $this->buildInstance->set('version', '0.0.1');
    $this->assertEquals(
        array_merge($this->defaultConfig, [
            'version' => '0.0.1'
        ]),
        $this->buildInstance->all()
    );
});

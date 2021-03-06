<?php

namespace Concrete\Tests\Core\Database\EntityManager\Provider;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Database\EntityManager\Provider\XmlProvider;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerXml;
use Concrete\Tests\Core\Database\Traits\DirectoryHelpers;

/**
 * XmlProviderTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class XmlProviderTest extends \PHPUnit_Framework_TestCase
{
    
    use DirectoryHelpers;
    
    /**
     * Stub of a package controller
     * 
     * @var PackageControllerYaml
     */
    private $packageStub;
    
    /**
     * Setup
     */
    public function setUp()
    {
        $this->app = Application::getFacadeApplication();
        $this->packageStub = new PackageControllerXml($this->app);
        parent::setUp();
    }
    
    /**
     * Test default mapping location and namespace for YamlProvidor
     */
    public function testGetDriversDefaultBehaviourSuccess()
    {
        $yamlProvider = new XmlProvider($this->packageStub);
        
        $drivers = $yamlProvider->getDrivers();
        // get c5 driver
        $c5Driver = $drivers[0];
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        // get Doctrine driver
        $driver = $c5Driver->getDriver();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\XmlDriver',$driver);
        $driverPaths = $driver->getLocator()->getPaths();
        $shortenedPath = $this->folderPathCleaner($driverPaths[0]);
        $this->assertEquals('config/xml', $shortenedPath);
        $driverNamespace = $c5Driver->getNamespace();
        $this->assertEquals('Concrete\Package\TestMetadatadriverXml\Entity', $driverNamespace);
    }
    
    /**
     * Test custom mapping location and namespace for YamlProvider
     */
    public function testGetDriversAddManuallyLocationAndNamespace(){
        $yamlProvider = new XmlProvider($this->packageStub, false);
        $namespace = 'MyNamespace\Some\Foo';
        $locations = array('mapping/xml', 'mapping/test/xml');
        $yamlProvider->addDriver($namespace, $locations);
        
        $drivers = $yamlProvider->getDrivers();
        // get c5 driver
        $c5Driver = $drivers[0];
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        // get Doctrine driver
        $driver = $c5Driver->getDriver();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\XmlDriver',$driver);
        $driverPaths = $driver->getLocator()->getPaths();
        $shortenedPath = $this->folderPathCleaner($driverPaths[0]);
        $this->assertEquals($locations[0], $shortenedPath);
        $driverNamespace = $c5Driver->getNamespace();
        $this->assertEquals($namespace, $driverNamespace);
    }
}

<?php

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

use ljvicente\BNC\Connection;
use ljvicente\BNC\Api\Company;

class CompanyTest extends TestCase
{
    private $company;

    public function setUp()
    {
        $dotenv = new Dotenv(__DIR__.'/../../..');
        $dotenv->load();

        $this->company = new Company(new Connection(getenv('BNC_USER'), getenv('BNC_PASS')));
        // filter test results
        $this->company = $this->company->filter(['company_name' => 'futtaim']);
    }

    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(Company::class, $this->company);
    }

    public function testCanGetDetails()
    {
        $this->assertNotEmpty($this->company->getDetails(43));
    }
    
    public function testCanGetContacts()
    {
        $this->assertNotEmpty($this->company->getContacts(43));
    }

    public function testCanGetKeyContacts()
    {
        $this->assertNotEmpty($this->company->getKeyContacts(43));
    }

    public function testCanGetProjects()
    {
        $this->assertNotEmpty($this->company->getProjects(43));
    }
}

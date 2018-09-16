<?php

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

use ljvicente\BNC\Connection;
use ljvicente\BNC\Api\Project;

class ProjectTest extends TestCase
{
    private $project;

    public function setUp()
    {
        $dotenv = new Dotenv(__DIR__.'/../../..');
        $dotenv->load();
        
        $this->project = new Project(new Connection(getenv('BNC_USER'), getenv('BNC_PASS')));
    }

    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(Project::class, $this->project);
    }

    public function testCanGetList()
    {
        $this->assertNotEmpty($this->project->getList());
        $this->assertNotEmpty($this->project->getLastSearchId());
    }

    public function testCanGetDetails()
    {
        $this->assertNotEmpty($this->project->getDetails(121150));
    }

    public function testCanGetCompanies()
    {
        $this->assertNotEmpty($this->project->getCompanies(121150));
    }

    public function testCanGetCompaniesByProjectNumber()
    {
        $this->assertNotEmpty($this->project->getCompaniesByProjectNumber('PRJAE17140021'));
    }

    public function testCanGetSchedulesByProjectNumber()
    {
        $this->assertNotEmpty($this->project->getSchedulesByProjectNumber('PRJAE17140021'));
    }

    public function testCanGetByProjectNumber()
    {
        $this->assertNotEmpty($this->project->getByProjectNumber('PRJAE17140021'));
    }

    public function testCanGetThumbByProjectNumber()
    {
        $this->assertContains('.jpg', $this->project->getThumbByProjectNumber('PRJAE17140021'));
    }

    public function testCanGetUpdatesByProjectNumber()
    {
        $this->assertNotEmpty($this->project->getUpdatesByProjectNumber('PRJAE16119850'));
    }

    public function testCanGetRecommendationsByProjectNumber()
    {
        $this->assertNotEmpty($this->project->getRecommendationsByProjectNumber('PRJAE16119850'));
    }
}

<?php

namespace ljvicente\BNC\Api;

use ljvicente\BNC\Connection;
use ljvicente\BNC\QueryTrait;

/**
 * Project-related endpoints.
 *
 * @author Leo <jemnuineuron@gmail.com>
 */
class Project
{
    use QueryTrait;

    /**
     * For reference
     */
    const PROJECT_STAGES = [
        'concept' => 10,
        'design' => 20,
        'tender' => 30,
        'under-construction' => 40,
        'on-hold' => 50,
        'cancelled' => 60,
        'completed' => 80,
    ];

    /**
     * Provide default filters because it's so slow
     * to retrieve all records.
     */
    const DEFAULT_FILTERS = [
        'call_frm' => 'search',
        'city' => '[6005,6008,6011,6003,6010,6543,6009,6004,6012]',
        'date_types' => '[]',
        'country' => '[223]',
        'offset' => 0,
        'search_st' => 'advanced_project',
        'search_tab' => 'project',
        'sort' => 'value',
        'sort_criteria' => 'desc',
    ];

    /**
     * BNC connection instance.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Custom filters.
     *
     * @var array
     */
    private $filters = [];

    private $last_search_id;

    /**
     * Accept Connection Instance and set default filters.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection = null)
    {
        $this->connection = $connection;
        $this->filters = self::DEFAULT_FILTERS;
    }

    /**
     * Get list of projects.
     *
     * @return array
     */
    public function getList()
    {
        // record last filter used for debugging purposes
        $_SESSION['project_last_filter'] = $this->buildQueryString($this->filters);

        $result = $this->connection->get('/projects/search/?' . $this->buildQueryString($this->filters));
        $this->last_search_id = $result['search_id'];

        return $result;
    }

    public function getLastSearchId()
    {
        return $this->last_search_id;
    }

    /**
     * Get details of a project.
     *
     * @param int $bnc_project_id
     * @return array
     */
    public function getDetails($bnc_project_id)
    {
        return $this->connection->get("/projects/{$bnc_project_id}/detail/");
    }

    /**
     * Get companies involved in a project.
     *
     * @param int $bnc_project_id
     * @return array
     */
    public function getCompanies($bnc_project_id)
    {
        return $this->connection->get("/projects/{$bnc_project_id}/companies/");
    }

    /**
     * Get companies involved by project.
     *
     * @param string $bnc_project_number
     * @return array
     */
    public function getCompaniesByProjectNumber($bnc_project_number)
    {
        return $this->getCompanies($this->extractProjectId($bnc_project_number));
    }

    /**
     * Get schedules per project.
     *
     * @param string $bnc_project_number
     * @return array
     */
    public function getSchedulesByProjectNumber($bnc_project_number)
    {
        $bnc_project_id = $this->extractProjectId($bnc_project_number);

        return $this->connection->get("/projects/{$bnc_project_id}/schedules/");
    }

    /**
     * Get project by BNC project number.
     *
     * @param string $bnc_project_number
     * @return array
     */
    public function getByProjectNumber($bnc_project_number)
    {
        // extract project_id from BNC project no.
        $project = $this->getDetails($this->extractProjectId($bnc_project_number));

        // if not existing, search
        if (count($project) == 0) {
            $projects = $this->filter([
                'keyword' => $bnc_project_number,
                'search_st' => 'keyword',
            ])->getList();

            if (isset($projects['projects'][0]['id'])) {
                return $this->getDetails($projects['projects'][0]['id']);
            }
            return null;
        }
        
        return $project;
    }

    public function getThumbByProjectNumber($bnc_project_number)
    {
        $project = $this->getByProjectNumber($bnc_project_number);

        if (isset($project['project']['s3_image_url'])) {
            return $project['project']['s3_image_url'];
        }
        return 'http://placehold.it/100x100?.jpg';
    }

    public function getUpdatesByProjectNumber($bnc_project_number)
    {
        $bnc_project_id = $this->extractProjectId($bnc_project_number);

        return $this->connection->get("/projects/{$bnc_project_id}/updates/?&offset=-1");
    }

    public function getRecommendationsByProjectNumber($bnc_project_number)
    {
        $bnc_project_id = $this->extractProjectId($bnc_project_number);

        return $this->connection->get("/users/recommendations/{$bnc_project_id}/");
    }

    /**
     * Extracts actual BNC id from the PRJ format.
     *
     * @param string $bnc_project_number
     * @return int
     */
    public function extractProjectId($bnc_project_number)
    {
        $bnc_project_number = trim($bnc_project_number);
        
        // stupid-proof id
        if (strpos(strtoupper($bnc_project_number), 'PRJ') === false) {
            // On a project number: PRJAE16118137
            // Some of them put 16118137 (which is wrong)
            // should be 118137.. So, we need to extract the id only
            // ..not the year!

            // note to future: if BNC projects reach 1m+, add another `9`
            // to condition. ;)
            if ($bnc_project_number > 999999) {
                return substr($bnc_project_number, 2);
            }
            return $bnc_project_number;
        }
        
        return substr($bnc_project_number, 7);
    }
}

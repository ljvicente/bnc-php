<?php

namespace ljvicente\BNC\Api;

use ljvicente\BNC\Connection;
use ljvicente\BNC\QueryTrait;

/**
 * Company-related endpoints.
 *
 * @author Leo <jemnuineuron@gmail.com>
 */
class Company
{
    use QueryTrait;

    /**
     * For reference
     */
    const COMPANY_TYPES = [
        'owner' => 1,
        'consultant' => 2,
        'contractor' => 3,
        'sub-contractor' => 4,
        'supplier' => 5,
    ];

    /**
     * Provide default filters because it's so slow
     * to retrieve all records.
     */
    const DEFAULT_FILTERS = [
        'call_frm' => 'search',
        'city' => '[6005,6008,6011,6003,6010,6543,6009,6004,6012]',
        'company_type' => '[1]',
        'country' => '[223]',
        'offset' => 0,
        'search_st' => 'advanced_company',
        'search_tab' => 'company',
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
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->filters = self::DEFAULT_FILTERS;
    }

    /**
     * Search companies with the provided filters.
     *
     * @return array
     */
    public function getList()
    {
        // record last filter used for debugging purposes
        $_SESSION['company_last_filter'] = $this->buildQueryString($this->filters);

        $result = $this->connection->get('/companies/search/?' . $this->buildQueryString($this->filters));
        $this->last_search_id = $result['search_id'];

        return $result;
    }

    public function getLastSearchId()
    {
        return $this->last_search_id;
    }

    /**
     * Search companies by name.
     *
     * @param string $company_name
     * @return array
     */
    public function getListByCompanyName($company_name)
    {
        $this->filters['company_name'] = $company_name;
        
        return $this->getList();
    }

    /**
     * Get details of a company by BNC id
     *
     * @param int $company_bnc_id
     * @return array
     */
    public function getDetails($company_bnc_id)
    {
        return $this->connection->get("/companies/{$company_bnc_id}/detail/");
    }

    /**
     * Get contact details
     *
     * @param int $company_bnc_id
     * @return array
     */
    public function getContacts($company_bnc_id)
    {
        return $this->connection->get("/companies/{$company_bnc_id}/contacts/");
    }

    /**
     * Get key contact details.
     *
     * @param int $company_bnc_id
     * @return array
     */
    public function getKeyContacts($company_bnc_id)
    {
        $company = $this->getDetails($company_bnc_id);
        
        return isset($company['company']['company_contacts']) ? $company['company']['company_contacts'] : [];
    }

    /**
     * Get company projects.
     *
     * @param int $company_bnc_id
     * @return array
     */
    public function getProjects($company_bnc_id)
    {
        return $this->connection->get("/companies/{$company_bnc_id}/references/");
    }
}

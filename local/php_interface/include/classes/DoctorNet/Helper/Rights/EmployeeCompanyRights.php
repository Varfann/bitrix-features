<?php

namespace DoctorNet\Helper\Rights;

/**
 * @property boolean $canView
 * @property boolean $canEdit
 * @property boolean $canViewDocument
 * @property boolean $canAddDocument
 * @property boolean $canDelDocument
 * @property boolean $canViewAccount
 * @property boolean $canRuleAccount
 * @property boolean $canViewDeals
 * @property boolean $canAddMarketplaces
 * @property boolean $canAddUser
 * @property boolean $canAudit
 * @property boolean $canAddStore
 */
class EmployeeCompanyRights extends AbstractHlBlockRights {
    
    protected $aliases = [
        'canAddMarketplaces' => 'canAddMplaces',
    ];
    
    /**
     * Set full right to Company
     *
     * @return $this
     */
    public function setFullRights() {
        $this->canView            = true;
        $this->canEdit            = true;
        $this->canViewDocument    = true;
        $this->canAddDocument     = true;
        $this->canDelDocument     = true;
        $this->canViewAccount     = true;
        $this->canRuleAccount     = true;
        $this->canViewDeals       = true;
        $this->canAddMarketplaces = true;
        $this->canAddUser         = true;
        $this->canAudit           = true;
        $this->canAddStore        = true;

        return $this;
    }
    
    protected function getHlBlockId() {
        return 3;
    }
    
    protected function getUserIdFieldName() {
        return 'UF_USER_ID';
    }
    
    protected function getLinkedIdFieldName() {
        return 'UF_COMPANY_ID';
    }
    
}

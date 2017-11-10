<?php

namespace DoctorNet\Helper\Rights;

/**
 * @property boolean $canView
 * @property boolean $canRule
 */
class EmployerCatalogRights extends AbstractHlBlockRights {
    
    /**
     * Set full right to Catalog
     *
     * @return $this
     */
    public function setFullRights() {
        $this->canView = true;
        $this->canRule = true;
        
        return $this;
    }
    
    protected function getHlBlockId() {
        return 4;
    }
    
    protected function getUserIdFieldName() {
        return 'UF_USER_ID';
    }
    
    protected function getLinkedIdFieldName() {
        return 'UF_CATALOG_ID';
    }
    
}
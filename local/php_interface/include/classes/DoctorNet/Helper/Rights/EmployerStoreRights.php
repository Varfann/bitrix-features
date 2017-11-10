<?php

namespace DoctorNet\Helper\Rights;

/**
 * @property boolean $canEdit
 */
class EmployerStoreRights extends AbstractHlBlockRights {
    

    /**
     * Set full right to Store
     *
     * @return $this
     */
    public function setFullRights() {
        $this->canEdit = true;
        
        return $this;
    }
    
    protected function getHlBlockId() {
        return 6;
    }
    
    protected function getUserIdFieldName() {
        return 'UF_USER_ID';
    }
    
    protected function getLinkedIdFieldName() {
        return 'UF_STORE_ID';
    }
    
}

<?

namespace DoctorNet\Helper\Rights;

/**
 * @property boolean $canView
 * @property boolean $canViewContacts
 * @property boolean $canViewLimit
 * @property boolean $canViewMarketplaces
 * @property boolean $canEdit
 * @property boolean $canAddStore
 * @property boolean $canDelete
 */
class EmployeeEmployeeRights extends AbstractHlBlockRights {
    
    protected $aliases = [
        'canViewMarketplaces' => 'canViewMplaces',
    ];
    
    /**
     * Set full right to employee
     *
     * @return $this
     */
    public function setFullRights() {
        $this->canDelete           = true;
        $this->canEdit             = true;
        $this->canView             = true;
        $this->canViewContacts     = true;
        $this->canViewLimit        = true;
        $this->canViewMarketplaces = true;
        
        return $this;
    }
    
    protected function getHlBlockId() {
        return 2;
    }
    
    protected function getUserIdFieldName() {
        return 'UF_USER_ID';
    }
    
    protected function getLinkedIdFieldName() {
        return 'UF_EMPLOYER_ID';
    }
}
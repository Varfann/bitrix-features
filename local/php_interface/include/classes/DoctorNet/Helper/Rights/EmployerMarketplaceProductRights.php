<?php

namespace DoctorNet\Helper\Rights;

/**
 * @property boolean $canRule
 * @property boolean $canAddToMarketplace
 * @property boolean $canAddToDeal
 */
class EmployerMarketplaceProductRights extends AbstractHlBlockRights {

    /**
     * Cap. We can't save rights.
     */
    public function save() {
        return;
    }

    /**
     * Cap. We haven't entity.
     */
    protected function getHlBlockId() {
        return -1;
    }

    /**
     * Cap. We haven't entity.
     */
    protected function getUserIdFieldName() {
        return '';
    }

    /**
     * Cap. We haven't entity.
     */
    protected function getLinkedIdFieldName() {
        return '';
    }

    /**
     * We can't load rights, but we can add rights map manually.
     */
    protected function loadRights() {
        $this->rights = [
            'canRule'             => false,
            'canAddToMarketplace' => false,
            'canAddToDeal'        => false,
        ];

        return;
    }
}
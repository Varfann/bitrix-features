<?php

namespace DoctorNet\Helper\Rights;

use Bitrix\Main\Data\Cache;
use DoctorNet\Helper\HighloadBlock\Entity;
use DoctorNet\Tools\Text;
use Larus\CacheManager;

abstract class AbstractHlBlockRights {
    
    protected $rowId;
    protected $rights;
    protected $rightsOriginal;
    
    protected $userId;
    protected $linkedId;
    
    protected $aliases = [];
    
    abstract protected function getHlBlockId();
    
    abstract protected function getUserIdFieldName();
    
    abstract protected function getLinkedIdFieldName();
    
    public function __construct($userId, $linkedId) {
        $this->userId   = $userId;
        $this->linkedId = $linkedId;
        $this->loadRights();
    }
    
    public function __destruct() {
        // $this->save(); // деструктор не гарантирует сохранения данных.
    }
    
    /**
     * @return int
     */
    public function save() {
        $isChanged = false;
        foreach ($this->rights as $key => $value) {
            if ($this->rightsOriginal[$key] != $value) {
                $isChanged = true;
                break;
            }
        }
        
        if (!$isChanged) {
            return $this->rowId;
        }
        
        $data                                = [];
        $data[$this->getUserIdFieldName()]   = $this->userId;
        $data[$this->getLinkedIdFieldName()] = $this->linkedId;
        foreach ($this->rights as $key => $value) {
            $data['UF_' . Text::camelCaseToUnderscored($key)] = $value ? 1 : 0;
        }
        
        /**
         * @var \Bitrix\Main\Entity\AddResult|\Bitrix\Main\Entity\UpdateResult $result
         */
        $result      = Entity::entityUpdateById($this->getHlBlockId(), $this->rowId, $data);
        $this->rowId = $result->getId();
    
        $this->dropCache();
        
        return $this->rowId;
    }
    
    public function dropCache() {
        $cache = Cache::createInstance();
        $cache->clean($this->getCacheId(), $this->getCacheDir());
    }
    
    /**
     * Loading existent rights
     *
     * @return $this
     */
    protected function loadRights() {
        $ttl   = 1 * 60 * 60; // 1 час
    
        $preparedRights = $this->prepareRights();
        
        $hlBlockId = $this->getHlBlockId();
        $userIdFieldName = $this->getUserIdFieldName();
        $userId = $this->userId;
        $LinkedIdFieldName = $this->getLinkedIdFieldName();
        $linkedId = $this->linkedId;
        
        $result = CacheManager::store($this->getCacheId(), $this->getCacheDir(), function() use ($hlBlockId, $userIdFieldName, $LinkedIdFieldName, $userId, $linkedId, $preparedRights) {
    
            $entity = Entity::getEntity(
                $hlBlockId, [
                              $userIdFieldName   => $userId,
                              $LinkedIdFieldName => $linkedId,
                          ]
            );
    
            $rowId = null;
            $rightsOriginal = $preparedRights;
            
            if ($entity) {
                $rowId = $entity['ID'];
                foreach ($entity as $key => $value) {
                    if ($key == 'ID'
                        || $key == $userIdFieldName
                        || $key == $LinkedIdFieldName
                    ) {
                        continue;
                    }
            
                    $fieldName = substr($key, 3); // remove "UF_" form start position
            
                    $rightsOriginal[Text::underscoredToCamelCase($fieldName)] = ($value == 1);
                }
            }
    
            return [
                'rowId'          => $rowId,
                'rightsOriginal' => $rightsOriginal
            ];
            
        }, $ttl);
        
        $this->rowId = $result['rowId'];
        $this->rightsOriginal = $result['rightsOriginal'];
        
        $this->rights = $this->rightsOriginal;
        
        return $this;
    }
    
    protected function getCacheId() {
        return $this->getHlBlockId() . 'u' . $this->userId . 'e' . $this->linkedId;
    }
    
    protected function getCacheDir() {
        return '/rights/'.$this->getHlBlockId().'/';
    }
    
    /**
     * Preparing empty rights array
     *
     * @return array
     */
    protected function prepareRights() {
        
        $cacheId = $this->getHlBlockId();
        $cachePath = '/rights/fields/';
        $ttl = 24 * 60 * 60; // 24 часа
    
        $hlBlockId = $this->getHlBlockId();
        $userIdFieldName = $this->getUserIdFieldName();
        $LinkedIdFieldName = $this->getLinkedIdFieldName();
            
        $fields = CacheManager::store($cacheId, $cachePath, function () use ($hlBlockId, $userIdFieldName, $LinkedIdFieldName) {
            $fieldsCollection = \CUserTypeEntity::GetList(
                [], [
                      'ENTITY_ID' => 'HLBLOCK_' . $hlBlockId,
                  ]
            );
            $fields           = [];
            while ($field = $fieldsCollection->Fetch()) {
                if ($field['FIELD_NAME'] == $userIdFieldName
                    || $field['FIELD_NAME'] == $LinkedIdFieldName
                ) {
                    continue;
                }
        
                $fieldName = substr($field['FIELD_NAME'], 3); // remove "UF_" form start position
        
                $fields[Text::underscoredToCamelCase($fieldName)] = false;
            }
            return $fields;
        }, $ttl);
        
        return $fields;
    }
    
    /**
     * @param string $name
     *
     * @return boolean
     */
    public function __get($name) {
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }
        
        if (is_null($this->rights[$name])) {
            return null;
        } // @TODO: throw RightNotFoundException
        
        return $this->rights[$name];
    }
    
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return boolean
     */
    public function __set($name, $value) {
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }
        
        if (is_null($this->rights[$name])) {
            return null;
        } // @TODO: throw RightNotFoundException
        
        return $this->rights[$name] = (boolean)$value;
    }
}
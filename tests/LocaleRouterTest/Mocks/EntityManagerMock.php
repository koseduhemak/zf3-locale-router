<?php


namespace LocaleRouterTest\Mocks;

use Doctrine\Common\Persistence\ObjectManager;

class EntityManagerMock implements ObjectManager
{
    public $persistedEntity;

    public function persist($entity)
    {
        $this->persistedEntity = $entity;
    }

    /**
     * @return mixed
     */
    public function getPersistedEntity()
    {
        return $this->persistedEntity;
    }

    /**
     * @param mixed $persistedEntity
     */
    public function setPersistedEntity($persistedEntity)
    {
        $this->persistedEntity = $persistedEntity;
    }

    public function find($className, $id)
    {
        // TODO: Implement find() method.
    }

    public function remove($object)
    {
        // TODO: Implement remove() method.
    }

    public function merge($object)
    {
        // TODO: Implement merge() method.
    }

    public function clear($objectName = null)
    {
        // TODO: Implement clear() method.
    }

    public function detach($object)
    {
        // TODO: Implement detach() method.
    }

    public function refresh($object)
    {
        // TODO: Implement refresh() method.
    }

    public function flush()
    {
        // TODO: Implement flush() method.
    }

    public function getRepository($className)
    {
        // TODO: Implement getRepository() method.
    }

    public function getClassMetadata($className)
    {
        // TODO: Implement getClassMetadata() method.
    }

    public function getMetadataFactory()
    {
        // TODO: Implement getMetadataFactory() method.
    }

    public function initializeObject($obj)
    {
        // TODO: Implement initializeObject() method.
    }

    public function contains($object)
    {
        // TODO: Implement contains() method.
    }
}

<?php

namespace DCS\DynamicDiscriminatorMapBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\DiscriminatorMap;

class DoctrineEventListener
{
    /**
     * @var array
     */
    protected $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $metadata = $event->getClassMetadata();
        $class = $metadata->getReflectionClass();


        if ($class === null) {
            $class = new \ReflectionClass($metadata->getName());
        }

        foreach ($this->mapping as $entityName => $map) {
            if ($class->getName() == $map['entity']) {
                $discriminatorMap = array();

                if ($discriminatorMapAttributes = $class->getAttributes(DiscriminatorMap::class)[0]?->getArguments() ?? null) {
                    $discriminatorMap = $discriminatorMapAttributes[0];
                }

                $discriminatorMap = array_merge($discriminatorMap, $map['map']);
                $discriminatorMap = array_merge($discriminatorMap, array($entityName => $map['entity']));

                $metadata->setDiscriminatorMap($discriminatorMap);
            }
        }
    }
}

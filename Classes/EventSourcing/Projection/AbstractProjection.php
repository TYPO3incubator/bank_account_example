<?php
namespace H4ck3r31\BankAccountExample\EventSourcing\Projection;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Event;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Generic\RevisionReference;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractEventEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;

/**
 * AbstractProjection
 */
abstract class AbstractProjection
{
    /**
     * @var bool
     */
    protected $force = false;

    /**
     * @var RevisionReference[][]
     */
    protected $revisionReferences;

    /**
     * @param bool $force
     * @return AbstractProjection
     */
    public function setForce(bool $force)
    {
        $this->force = $force;
        return $this;
    }

    /**
     * @param AbstractEventEntity $entity
     * @return RevisionReference
     */
    protected function getRevisionReference(AbstractEventEntity $entity)
    {
        $className = get_class($entity);
        return ($this->revisionReferences[$className][$entity->getUuid()] ?? null);
    }

    /**
     * @param AbstractEventEntity $entity
     */
    protected function purgeRevisionReference(AbstractEventEntity $entity)
    {
        $className = get_class($entity);
        if ($this->getRevisionReference($entity) !== null) {
            unset($this->revisionReferences[$className][$entity->getUuid()]);
        }
    }

    /**
     * @param AbstractEventEntity $entity
     * @return bool
     */
    protected function equalsRevision(AbstractEventEntity $entity)
    {
        $revisionReference = $this->getRevisionReference($entity);
        return ($revisionReference !== null && $revisionReference->getRevision() === $entity->getRevision());
    }

    /**
     * @return Session
     */
    protected function getPersistenceSession()
    {
        return Common::getObjectManager()->get(Session::class);
    }
}

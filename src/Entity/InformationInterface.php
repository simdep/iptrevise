<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 */

namespace App\Entity;

use DateTime;

/**
 * Interface InformationInterface.
 *
 * @category Entity
 */
interface InformationInterface
{
    /**
     * Return date time creation.
     *
     * @return DateTime | null
     */
    public function getCreated(): ?DateTime;

    /**
     * Return creator (user entity).
     *
     * @return User | null
     */
    public function getCreator(): ?User;

    /**
     * Return date time of the last update.
     *
     * @return DateTime | null
     */
    public function getUpdated(): ?DateTime;
}

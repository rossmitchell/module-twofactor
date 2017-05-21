<?php
/**
 * A Magento 2 module named Rossmitchell/Twofactor
 * Copyright (C) 2017
 *
 * This file is part of Rossmitchell/Twofactor.
 *
 * Rossmitchell/Twofactor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Rossmitchell\Twofactor\Api\Request;

interface GetQrCodeManagementInterface
{
    /**
     * GET for getQrCode api
     * @return \Rossmitchell\Twofactor\Api\Response\GetQrCodeInterface
     */
    public function getQrCode();
}

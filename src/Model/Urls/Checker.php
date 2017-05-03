<?php
/**
 * A two factor authentication module that protects both the admin and customer logins
 * Copyright (C) 2017  Ross Mitchell
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

namespace Rossmitchell\Twofactor\Model\Urls;

use Magento\Framework\UrlInterface;

/**
 * Class Checker
 *
 * This is used as a way to check is a user is on a certain page. It provides semantic names for this and the ability
 * to check either customer or admin users
 */
class Checker
{
    /**
     * @var Fetcher
     */
    private $fetcher;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Checker constructor.
     * @param Fetcher $fetcher
     * @param UrlInterface $url
     */
    public function __construct(Fetcher $fetcher, UrlInterface $url)
    {
        $this->fetcher = $fetcher;
        $this->url = $url;
    }

    /**
     * Checks if the user is currently on the two factor authentication page. Can be used to check both customers and
     * admin users.
     *
     * @param bool $forAdmin - true to check for admin users, false to check for customers
     * @return bool
     */
    public function areWeOnTheAuthenticationPage($forAdmin = false)
    {
        $authenticationUrl = $this->fetcher->getAuthenticationUrl($forAdmin);

        return $this->compareUrls($this->getCurrentUrl(), $authenticationUrl);
    }

    /**
     * Checks if the user is currently on the two factor verification page. Can be used to check both customers and
     * admin users.
     *
     * @param bool $forAdmin - true to check for admin users, false to check for customers
     * @return bool
     */
    public function areWeOnTheVerificationPage($forAdmin = false)
    {
        $verificationUrl = $this->fetcher->getVerificationUrl($forAdmin);

        return $this->compareUrls($this->getCurrentUrl(), $verificationUrl);
    }

    /**
     * Returns the current URL
     *
     * @return string
     */
    private function getCurrentUrl()
    {
        return $this->url->getCurrentUrl();
    }

    /**
     * Compares two URLs.
     *
     * @param $firstUrl
     * @param $secondUrl
     * @return bool
     */
    private function compareUrls($firstUrl, $secondUrl)
    {
        return ($this->cleanUrl($firstUrl) === $this->cleanUrl($secondUrl));
    }

    /**
     * This is used normalise the URLs to a known structure and remove any trailing characters that could prevent them
     * from getting checked correctly.
     *
     * For these checks I'm not that conceded if the customer is using http or https so we strip that out. Equally I'm
     * not worried about ports, or query params so they are removed as well
     *
     * @param $url
     * @return string
     */
    private function cleanUrl($url)
    {
        $parts = parse_url($url);
        $cleanUrl = $parts['host'] . '/' . $parts['path'];
        $noRewriteUrl = str_replace('/index.php', '', $cleanUrl);

        return trim($noRewriteUrl, '\t\n\r/');
    }
}
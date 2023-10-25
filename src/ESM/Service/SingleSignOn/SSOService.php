<?php

declare(strict_types=1);

namespace App\ESM\Service\SingleSignOn;

use Doctrine\ORM\EntityManagerInterface;

class SSOService
{
    /** @var string */
    public const ROUTE_SET_TOKEN = 'sso.set_token';
    /** @var string */
    public const GET_SP_PARAM = 'sp-url';
    /** @var string */
    public const SESSION_SP_URL_KEY = 'cfsso_sp';

    /** @var EntityManagerInterface */
    private $em;

    /**
     * TOSService constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return string
     */
    public function getDomain(?string $url): ?string
    {
        try {
            $urlInfos = parse_url($url);

            $url = $urlInfos['scheme'].'://'.$urlInfos['host'];
            $url .= !empty($urlInfos['port']) ? ':'.$urlInfos['port'] : '';

            return $url;
        } catch (\Exception $e) {
            return null;
        }
    }
}

<?php

namespace Schrojf\Papers\Traits;

trait SponsorSupportable
{
    private static bool $displaySupportButton = true;

    /**
     * Hide the sponsor badge if the provided phrase is recognized as valid support.
     */
    public static function hideSponsorBadge(string $phrase): void
    {
        if (self::isValidSupportPhrase($phrase)) {
            self::$displaySupportButton = false;
        }
    }

    /**
     * Hide the sponsor badge if the provided phrase is recognized as valid support.
     */
    public static function acknowledgeSupport(string $phrase): void
    {
        self::hideSponsorBadge($phrase);
    }

    /**
     * Determine if the sponsor badge should be shown.
     */
    public static function shouldShowSponsorBadge(): bool
    {
        return self::$displaySupportButton;
    }

    /**
     * Check if the given phrase matches any valid support hash.
     */
    private static function isValidSupportPhrase(string $phrase): bool
    {
        $validHashes = [
            '8c6e7cd2',
            'a518c4fb',
            '6bac0444',
        ];

        return in_array(hash('crc32b', $phrase), $validHashes, true);
    }
}

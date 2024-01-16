<?php

namespace App\Enum;

class AdvertisementStatus
{
    public const DRAFT = 'draft';
    public const REVIEW = 'review';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';

    public static function getStatuses(): array
    {
        return [
            self::DRAFT,
            self::REVIEW,
            self::PUBLISHED,
            self::ARCHIVED,
        ];
    }
}

<?php

namespace Twomedia\PoliciesBuilder\DTOs;

class ResolvedPolicy
{
    public function __construct(
        public readonly string $policyType,
        public readonly string $locale,
        public readonly string $metaTitle,
        public readonly string $metaDescription,
        public readonly string $content,
    ) {}

    public function toArray(): array
    {
        return [
            'policy_type' => $this->policyType,
            'locale' => $this->locale,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'content' => $this->content,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            policyType: $data['policy_type'],
            locale: $data['locale'],
            metaTitle: $data['meta_title'],
            metaDescription: $data['meta_description'] ?? '',
            content: $data['content'],
        );
    }
}

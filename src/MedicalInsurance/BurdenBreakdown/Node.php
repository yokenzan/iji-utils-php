<?php

declare(strict_types=1);

namespace IjiUtils\MedicalInsurance\BurdenBreakdown;

use IjiUtils\MedicalInsurance\BenefitWays\BenefitCategory;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;
use JsonSerializable;

class Node implements JsonSerializable
{
    private ?string          $label;
    private ?string          $description;
    private Amount           $amount;
    private ?BenefitCategory $category;
    private ?Node            $left;
    private ?Node            $right;
    private ?Node            $parent;

    public function __construct(
        ?string          $label = null,
        ?string          $description = null,
        Amount           $amount,
        ?BenefitCategory $category = null
    ) {
        $this->label       = $label;
        $this->description = $description;
        $this->amount      = $amount;
        $this->category    = $category;
        $this->parent      = null;
        $this->left        = null;
        $this->right       = null;
    }

    public function isLeaf(): bool
    {
        return is_null($this->left) && is_null($this->right);
    }

    public function addLeft(Node $node): self
    {
        $this->left = $node;
        $node->setParent($this);

        return $this;
    }

    public function addRight(Node $node): self
    {
        $this->right = $node;
        $node->setParent($this);

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getRoot(): Node
    {
        $parent = $this->parent;

        if (is_null($parent)) {
            return $this;
        }

        return $parent->getRoot();
    }

    public function getLeft(): ?Node
    {
        return $this->left;
    }

    public function getRight(): ?Node
    {
        return $this->right;
    }

    public function getCategory(): ?BenefitCategory
    {
        return $this->category;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        $values = [
            'label'  => $this->getLabel(),
            'amount' => (string)$this->getAmount(),
        ];

        if ($this->getLeft()) {
            $values['left'] = $this->getLeft()->jsonSerialize();
        }
        if ($this->getRight()) {
            $values['right'] = $this->getRight()->jsonSerialize();
        }

        return json_encode($values);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    protected function setParent(Node $parent): self
    {
        $this->parent = $parent;
        return $this;
    }
}

<?php

namespace App\Entity;

use Bupy7\Doctrine\NestedSet\NestedSetInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 * @ORM\Table(
 *     name="items",
 *     indexes={
 *      @ORM\Index(columns={"name"})
 *     },
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(columns={"code"})
 *     }
 * )
 */
class Item implements NestedSetInterface
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    public $code;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    public $name;

    /**
     * @ORM\Column(type="integer", name="root_key")
     * @var int
     */
    private $rootKey = 1;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $level = 1;

    /**
     * @ORM\Column(type="integer", name="left_key")
     * @var int
     */
    private $leftKey;

    /**
     * @ORM\Column(type="integer", name="right_key")
     * @var int
     */
    private $rightKey;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): NestedSetInterface
    {
        $this->id = $id;
        return $this;
    }

    public function getRootKey(): int
    {
        return $this->rootKey;
    }

    public function setRootKey(int $rootKey): NestedSetInterface
    {
        $this->rootKey = $rootKey;
        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): NestedSetInterface
    {
        $this->level = $level;
        return $this;
    }

    public function getLeftKey(): int
    {
        return $this->leftKey;
    }

    public function setLeftKey(int $leftKey): NestedSetInterface
    {
        $this->leftKey = $leftKey;
        return $this;
    }

    public function getRightKey(): int
    {
        return $this->rightKey;
    }

    public function setRightKey(int $rightKey): NestedSetInterface
    {
        $this->rightKey = $rightKey;
        return $this;
    }
}
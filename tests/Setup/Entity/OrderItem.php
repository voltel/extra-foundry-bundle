<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_item")
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Product|null
     * @ORM\ManyToOne(targetEntity=Product::class)
     */
    private $product;

    /**
     * @var integer|null
     * @ORM\Column(type="integer")
     */
    private $unitCount;

    /**
     * @var Order|null
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderIterCollection")
     */
    private $order;

    /**
     * @var string|null
     * @ORM\Column(type="text")
     */
    private $notes;

    /**
     * OrderItem constructor.
     */
    public function __construct(
        ?Order $order = null,
        ?Product $product = null,
        ?int $units = null
    )
    {
        $this->product = $product;
        $this->unitCount = $units;
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return OrderItem
     */
    public function setProduct(?Product $product): OrderItem
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUnitCount(): ?int
    {
        return $this->unitCount;
    }

    /**
     * @param int|null $unitCount
     * @return OrderItem
     */
    public function setUnitCount(?int $unitCount): OrderItem
    {
        $this->unitCount = $unitCount;
        return $this;
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @return OrderItem
     */
    public function setOrder(?Order $order): OrderItem
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return OrderItem
     */
    public function setNotes(?string $notes): OrderItem
    {
        $this->notes = $notes;
        return $this;
    }



}
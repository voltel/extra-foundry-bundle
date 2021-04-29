<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`order`")
 */
class Order
{
    public const STATUS_CREATED = 'created';
    public const STATUS_CHECKEDOUT = 'checkedout';
    public const STATUS_SENT = 'sent';
    public const STATUS_AWAITING = 'awaiting';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [self::STATUS_CREATED, self::STATUS_CHECKEDOUT, self::STATUS_SENT, self::STATUS_AWAITING, self::STATUS_DELIVERED, self::STATUS_DELIVERED, self::STATUS_CANCELLED];


    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=10)
     */
    private $status = self::STATUS_CREATED;

    /**
     * @var Customer|null
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="orderCollection")
     */
    private $customer;

    /**
     * @var Address|null
     * @ORM\ManyToOne(targetEntity=Address::class)
     */
    private $deliveryAddress;

    /**
     * @var Address|null
     * @ORM\ManyToOne(targetEntity=Address::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $billingAddress;

    /**
     * @var Collection|OrderItem[]
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="order",
     *     cascade={"persist", "remove"})
     */
    private $orderItemCollection;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $orderedAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deliveredAt;

    /**
     * Order constructor.
     */
    public function __construct(
        ?Customer $customer = null
    )
    {
        $this->customer = $customer;

        $this->orderItemCollection = new ArrayCollection();

        $this->orderedAt = new \DateTimeImmutable('now');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItemCollection()
    {
        return $this->orderItemCollection;
    }

    //<editor-fold desc="Add and Remove elements to/from $this->orderItemCollection">
    public function addElementToOrderIterCollection(OrderItem $orderItem)
    {
        if ($this->orderItemCollection->contains($orderItem)) return;
        $this->orderItemCollection->add($orderItem);
        $orderItem->setOrder($this);
    }

    public function removeElementFromOrderIterCollection(OrderItem $orderItem)
    {
        if (!$this->orderItemCollection->contains($orderItem)) return;
        $this->orderItemCollection->removeElement($orderItem);
        $orderItem->setOrder(null);
    }
    //</editor-fold>

    /**
     * @return \DateTimeImmutable
     */
    public function getOrderedAt(): \DateTimeImmutable
    {
        return $this->orderedAt;
    }

    /**
     * @return Address|null
     */
    public function getDeliveryAddress(): ?Address
    {
        return $this->deliveryAddress;
    }

    /**
     * @param Address|null $deliveryAddress
     * @return Order
     */
    public function setDeliveryAddress(?Address $deliveryAddress): Order
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * @return Address|null
     */
    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    /**
     * @param Address|null $billingAddress
     * @return Order
     */
    public function setBillingAddress(?Address $billingAddress): Order
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeliveredAt(): ?\DateTime
    {
        return $this->deliveredAt;
    }

    /**
     * @param \DateTime|null $deliveredAt
     * @return Order
     */
    public function setDeliveredAt(?\DateTime $deliveredAt): Order
    {
        $this->deliveredAt = $deliveredAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Order
     */
    public function setStatus(?string $status): Order
    {
        $this->validateNewStatus($status);

        $this->status = $status;
        return $this;
    }


    private static function validateStatus(string $c_status)
    {
        if (!in_array($c_status, self::STATUSES)) {
            throw new \LogicException(sprintf('Unknown order status "%s" ', $c_status));
        }
    }

    private static function getStatusIndexAmongAllStatuses(string $c_status): int
    {
        self::validateStatus($c_status);
        return array_search($c_status, self::STATUSES);
    }

    public function getNextStatus(): ?string
    {
        if ($this->status === self::STATUS_DELIVERED) return null;
        if ($this->status === self::STATUS_CANCELLED) return null;

        $n_current_status_index = self::getStatusIndexAmongAllStatuses($this->status);

        return self::STATUSES[$n_current_status_index + 1];
    }

    private function validateNewStatus(string $c_status)
    {
        if (is_null($this->status)) return;

        $n_current_status_index = self::getStatusIndexAmongAllStatuses($this->status);
        $n_new_status_index = self::getStatusIndexAmongAllStatuses($c_status);

        if ($n_new_status_index < $n_current_status_index) {
            throw new \LogicException(sprintf('You cannot assign order status "%s" when current status is "%s".', $c_status, $this->status));
        }
    }

}
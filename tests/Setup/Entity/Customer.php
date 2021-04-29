<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="customer")
 */
class Customer
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isOrganization;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $staffCount;

    /**
     * @var Address[]|null
     * @ORM\OneToMany(targetEntity="Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Address", mappedBy="customer",
     *     cascade={"persist", "remove"})
     */
    private $addressCollection;

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        $this->addressCollection = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return Customer
     */
    public function setFirstName(?string $firstName): Customer
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return Customer
     */
    public function setLastName(?string $lastName): Customer
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOrganization(): bool
    {
        return $this->isOrganization;
    }

    /**
     * @param bool $isOrganization
     * @return Customer
     */
    public function setIsOrganization(bool $isOrganization): Customer
    {
        $this->isOrganization = $isOrganization;
        return $this;
    }

    /**
     * @return Collection|Address[]|null
     */
    public function getAddressCollection(): Collection
    {
        return $this->addressCollection;
    }

    /**
     * @param Address[]|null $addressCollection
     * @return Customer
     */
    public function setAddressCollection(?array $addressCollection): Customer
    {
        $this->addressCollection = new ArrayCollection($addressCollection);
        foreach ($this->addressCollection as $oThisAddress) {
            $oThisAddress->setCustomer($this);
        }
        return $this;
    }


    //<editor-fold desc="Add and Remove elements to/from $this->addressCollection">
    public function addElementToAddressCollection(Address $address)
    {
        if ($this->addressCollection->contains($address)) return;
        $this->addressCollection->add($address);
        $address->setCustomer($this);
    }

    public function removeElementFromAddressCollection(Address $address)
    {
        if (!$this->addressCollection->contains($address)) return;
        $this->addressCollection->removeElement($address);
        $address->setCustomer(null);
    }
    //</editor-fold>

    /**
     * @return int|null
     */
    public function getStaffCount(): ?int
    {
        return $this->staffCount;
    }

    /**
     * @param int|null $staffCount
     */
    public function setStaffCount(?int $staffCount): void
    {
        $this->staffCount = $staffCount;
    }


}
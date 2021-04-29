<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Entity;


use Doctrine\ORM\Mapping as ORM;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Customer;

/**
 * @ORM\Entity
 * @ORM\Table(name="address")
 */
class Address
{
    /**
     * @var integer|null
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=6)
     */
    private $countryCode;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100)
     */
    private $cityName;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100)
     */
    private $cityAreaName;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $addressName;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Customer", inversedBy="addressCollection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;


    /**
     */
    public function __construct(
        ?string $countryCode = null,
        ?string $cityName = null,
        ?string $cityAreaName = null,
        ?string $addressName = null)
    {
        $this->countryCode = $countryCode;
        $this->cityName = $cityName;
        $this->cityAreaName = $cityAreaName;
        $this->addressName = $addressName;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     * @return Address
     */
    public function setCountryCode(?string $countryCode): Address
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    /**
     * @param string|null $cityName
     * @return Address
     */
    public function setCityName(?string $cityName): Address
    {
        $this->cityName = $cityName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCityAreaName(): ?string
    {
        return $this->cityAreaName;
    }

    /**
     * @param string|null $cityAreaName
     * @return Address
     */
    public function setCityAreaName(?string $cityAreaName): Address
    {
        $this->cityAreaName = $cityAreaName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressName(): ?string
    {
        return $this->addressName;
    }

    /**
     * @param string|null $addressName
     * @return Address
     */
    public function setAddressName(?string $addressName): Address
    {
        $this->addressName = $addressName;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return Address
     */
    public function setCustomer(Customer $customer): Address
    {
        $this->customer = $customer;
        return $this;
    }

}
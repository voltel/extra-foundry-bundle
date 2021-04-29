<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
    public const CARS = 'Cars';
    public const JEWELRY = 'Jewelry';
    public const FURNITURE = 'Furniture';
    public const APARTMENTS = 'Apartments';
    public const VEHICLES = 'Vehicles';
    public const HOUSES = 'Houses';
    public const ACCOMMODATION = 'Accommodation';
    public const LUXURY = 'Luxury';
    public const ORDINARY = 'Ordinary';

    public const CATEGORIES = [
        self::CARS,
        self::JEWELRY,
        self::FURNITURE,
        self::APARTMENTS,
        self::VEHICLES,
        self::HOUSES,
        self::LUXURY,
        self::ORDINARY,
    ];

    /**
     * @var integer|null
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private $categoryName;

    /**
     */
    public function __construct(
        ?string $categoryName = null
    )
    {
        $this->categoryName = $categoryName;
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
    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    /**
     * @param string|null $categoryName
     * @return Category
     */
    public function setCategoryName(?string $categoryName): Category
    {
        $this->categoryName = $categoryName;
        return $this;
    }

}
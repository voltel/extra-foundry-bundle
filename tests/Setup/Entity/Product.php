<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 */
class Product
{
    public const SLUG_LENGTH_MAX = 50;
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
    private $productName;

    /**
     * This property is to demonstrate and test the use of model factory's
     * "afterPersist" callback. The "ProductFactory" defines the "afterPersist" callback
     * that utilizes the id of a just persisted Product to generate a slug.
     *
     * @var string|null
     * @ORM\Column(type="string", length=Product::SLUG_LENGTH_MAX, nullable=true)
     */
    private $slug;

    /**
     * @var Collection|Category[]|null
     * @ORM\ManyToMany(targetEntity=Category::class, cascade={"persist"})
     */
    private $categoryCollection;

    /**
     * @var bool|null
     * @ORM\Column(type="string")
     */
    private $inPromotion;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $registeredAt;

    /**
     */
    public function __construct(string $productName = null)
    {
        $this->productName = $productName;

        $this->registeredAt = new \DateTimeImmutable('now');

        $this->categoryCollection = new ArrayCollection();
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
    public function getProductName(): ?string
    {
        return $this->productName;
    }

    /**
     * @param string|null $productName
     * @return Product
     */
    public function setProductName(string $productName): Product
    {
        $this->productName = $productName;
        return $this;
    }

    /**
     * @return Category[]|null
     */
    public function getCategoryCollection(): Collection
    {
        return $this->categoryCollection;
    }

    public function addElementToCategoryCollection(Category $category)
    {
        if ($this->categoryCollection->contains($category)) return;
        $this->categoryCollection->add($category);
    }

    public function removeElementFromCategoryCollection(Category $category)
    {
        if (!$this->categoryCollection->contains($category)) return;
        $this->categoryCollection->removeElement($category);
    }


    /**
     * @return bool|null
     */
    public function isInPromotion(): ?bool
    {
        return $this->inPromotion;
    }

    /**
     * @param bool|null $inPromotion
     * @return Product
     */
    public function setInPromotion(bool $inPromotion): Product
    {
        $this->inPromotion = $inPromotion;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     * @return Product
     */
    public function setSlug(?string $slug): Product
    {
        $this->slug = $slug;
        return $this;
    }


}
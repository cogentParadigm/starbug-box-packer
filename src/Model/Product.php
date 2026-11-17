<?php
namespace Starbug\BoxPacker\Model;

use DVDoug\BoxPacker\Item;
use DVDoug\BoxPacker\Rotation;

class Product implements Item {
  public function __construct(
    protected array $product
  ) {
  }
  public function getProduct(): array {
    return $this->product;
  }
  public function getDescription(): string {
    return $this->product["description"];
  }
  public function getWidth(): int {
    return $this->getDimension("width");
  }
  public function getLength(): int {
    return $this->getDimension("length");
  }
  public function getDepth(): int {
    return $this->getDimension("height");
  }
  public function getWeight(): int {
    return ceil($this->product["weight"] / 10 * 453.592);
  }
  public function getAllowedRotation(): Rotation {
    return Rotation::BestFit;
  }
  protected function getDimension($field): int {
    return $this->product[$field] / 10 * 25.4;
  }
  protected function getPrice(): int {
    return $this->product["price"];
  }
  protected function getQuantity(): int {
    return $this->product["qty"];
  }
}

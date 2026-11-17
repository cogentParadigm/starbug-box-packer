<?php
namespace Starbug\BoxPacker\Model;

use DVDoug\BoxPacker\Box;

class ShippingBox implements Box {
  public function __construct(
    protected array $box
  ) {
  }
  public function getReference(): string {
    return $this->box["name"];
  }
  public function getOuterWidth(): int {
    return $this->getDimension("width");
  }
  public function getOuterLength(): int {
    return $this->getDimension("length");
  }
  public function getOuterDepth(): int {
    return $this->getDimension("height");
  }
  public function getEmptyWeight(): int {
    return $this->getWeight("weight");
  }
  public function getInnerWidth(): int {
    return $this->getDimension("inner_width");
  }
  public function getInnerLength(): int {
    return $this->getDimension("inner_length");
  }
  public function getInnerDepth(): int {
    return $this->getDimension("inner_height");
  }
  public function getMaxWeight(): int {
    return $this->getWeight("maximum_weight");
  }
  protected function getDimension($field): int {
    return ceil($this->box[$field] / 100 * 25.4);
  }
  protected function getWeight($field): int {
    return ceil($this->box[$field] / 100 * 453.592);
  }
}

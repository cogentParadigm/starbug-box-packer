<?php
namespace Starbug\BoxPacker;

use DVDoug\BoxPacker\Packer;
use Starbug\BoxPacker\Db\Query\ShippingBoxesRepository;
use Starbug\BoxPacker\Model\Product;
use Starbug\BoxPacker\Model\ShippingBox;

class PackerFactory {
  public function __construct(
    protected ShippingBoxesRepository $boxes
  ) {
  }
  public function create($products = [], ?float $maxBoxWeight = null): Packer {
    $boxes = $this->getBoxes();
    $packer = new Packer();
    // $packer->setMaxBoxesToBalanceWeight(0);
    foreach ($boxes as $box) {
      if ($maxBoxWeight !== null) {
        $limitInHundredths = (int) round($maxBoxWeight * 10);
        $existingMax = (int) ($box["maximum_weight"] ?? 0);
        if ($existingMax == 0 || $limitInHundredths < $existingMax) {
          $box["maximum_weight"] = $limitInHundredths;
        }
      }
      $packer->addBox(new ShippingBox($box));
    }
    foreach ($products as $product) {
      $qty = $product["qty"] ?? 1;
      $item = new Product($product);
      for ($i = 1; $i <= $qty; $i++) {
        $packer->addItem($item);
      }
    }
    return $packer;
  }
  public function getBoxes() {
    static $boxes;
    if (empty($boxes)) {
      $boxes = $this->boxes->getBoxes();
    }
    return $boxes;
  }
}

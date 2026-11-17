<?php
namespace Starbug\BoxPacker;

use DVDoug\BoxPacker\Exception\NoBoxesAvailableException;

class ProductBoxPacker {
  public function __construct(
    protected PackerFactory $factory
  ) {
  }
  public function hasBoxes() {
    $boxes = $this->factory->getBoxes();
    return !empty($boxes);
  }
  public function getPackages($products, $selfBoxed = [], ?float $maxBoxWeight = null) {
    if (!$this->hasBoxes()) {
      return $products;
    }
    $packer = $this->factory->create($products, $maxBoxWeight);
    try {
      $boxes = $packer->pack();
    } catch (NoBoxesAvailableException $e) {
      $failedProduct = $e->getAffectedItems()->extract()->getProduct();
      for ($i = 0; $i < intval($failedProduct['qty']); $i++) {
        $selfBoxed[] = [
          'width' => $failedProduct['width'],
          'length' => $failedProduct['length'],
          'height' => $failedProduct['height'],
          'weight' => $failedProduct['weight'],
          'description' => $failedProduct['description'],
          'volume_used' => 100,
          'items' => [
            [
              "width" => $failedProduct['width'],
              "length" => $failedProduct['length'],
              "depth" => $failedProduct['height'],
              "weight" => $failedProduct['weight'],
              "price" => $failedProduct['price'],
              "item" => $failedProduct
            ]
          ]
        ];
      }

      $filteredProducts = array_values(array_filter($products, function ($p) use ($failedProduct) {
        return $p["sku"] !== $failedProduct["sku"];
      }));
      return $this->getPackages($filteredProducts, $selfBoxed, $maxBoxWeight);
    }
    $packages = [];
    foreach ($boxes as $box) {
      $type = $box->box;
      $items = [];
      foreach ($box->items as $item) {
        $data = $item->jsonSerialize();
        $data["width"] = $item->item->getProduct()["width"];
        $data["length"] = $item->item->getProduct()["length"];
        $data["depth"] = $item->item->getProduct()["height"];
        $data["weight"] = $item->item->getProduct()["weight"];
        $data["price"] = $item->item->getProduct()["price"];
        $data["qty"] = $item->item->getProduct()["qty"];
        $items[] = $data;
      }
      $packages[] = [
        "width" => round($type->getOuterWidth() / 25.4 * 10),
        "length" => round($type->getOuterLength() / 25.4 * 10),
        "height" => round($type->getOuterDepth() / 25.4 * 10),
        "weight" => round($box->getWeight() / 453.592 * 10),
        "description" => $type->getReference(),
        "volume_used" => $box->getVolumeUtilisation(),
        "items" => $items
      ];
    }
    return array_merge($selfBoxed, $packages);
  }
}

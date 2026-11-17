<?php
namespace Starbug\BoxPacker\Db\Query;

use Starbug\Db\DatabaseInterface;

class ProductsRepository {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function getProducts($ids) {
    if (!is_array($ids)) {
      $ids = explode(",", $ids);
    }
    $query = $this->db->query("products")
      ->condition("products.id", $ids)
      ->select(["sku", "name as description", "width", "length", "height", "weight", "price"], "products");
    return $query->all();
  }
}

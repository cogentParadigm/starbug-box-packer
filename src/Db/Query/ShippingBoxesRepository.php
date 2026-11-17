<?php
namespace Starbug\BoxPacker\Db\Query;

use Starbug\Db\DatabaseInterface;

class ShippingBoxesRepository {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function getBoxes() {
    return $this->db->query("shipping_boxes")
      ->all();
  }
}

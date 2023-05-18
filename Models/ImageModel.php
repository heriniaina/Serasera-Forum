<?php 

namespace Serasera\Forum\Models;
use Serasera\Base\Models\BaseModel;

class ImageModel extends BaseModel {

    protected $table = "ci_forum_images";
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id', 'date', 'username', 'file'];


}
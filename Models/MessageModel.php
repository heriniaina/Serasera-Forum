<?php 

namespace Serasera\Forum\Models;
use Serasera\Base\Models\BaseModel;

class MessageModel extends BaseModel {

    protected $table = "ci_forum_messages";
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id', 'mid', 'pid', 'tid', 'title', 'message', 'email', 'username', 'date', 'replies', 'last_date', 'hits', 'last_username', 'last_mid', 'last_message', 'notify'];


}
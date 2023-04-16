<?php 

namespace Serasera\Forum\Models;
use Serasera\Base\Models\BaseModel;

class TopicModel extends BaseModel {

    protected $table = "ci_forum_topics";
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id', 'tid', 'title', 'description', 'email', 'username', 'date', 'gid', 'last_date', 'messages', 'last_username', 'last_mid', 'last_message', 'last_title'];


}
<?php 

namespace Serasera\Forum\Models;
use Serasera\Base\Models\BaseModel;

class MessageModel extends BaseModel {

    protected $table = "ci_forum_messages";
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id', 'mid', 'pid', 'tid', 'title', 'message', 'email', 'username', 'date', 'replies', 'last_date', 'hits', 'last_username', 'last_mid', 'notify'];


    public function getMessagesWithTopic() {
        
        $this->select('m.*, t.title as topic')
        ->from('ci_forum_messages m', true)
        ->join('ci_forum_topics t', 'm.tid=t.tid', 'left');

        return $this;

    }
}
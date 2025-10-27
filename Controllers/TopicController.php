<?php 

namespace Serasera\Forum\Controllers;
use Serasera\Base\Controllers\BaseController;
use Serasera\Forum\Models\MessageModel;
use Serasera\Forum\Models\TopicModel;

class TopicController extends BaseController {

    public function index() {
        $topicModel = new TopicModel();

        $this->data['page_title'] = lang('Forum.topic_list');


        
        if(!is_null($this->user)) {
            $topicModel->where("(username = '" .$this->user['username']. "' OR gid IN ('".implode("', '", $this->user['groups'])."')) ");
        }
        $this->data['topics'] = $topicModel->orderBy('title')->findAll();

        return view('\Serasera\Forum\Views\topic_index', $this->data);
        
    }
    public function show($tid) {

        $topicModel = new TopicModel();

        
        $builder = $topicModel->where('tid', $tid);
        if(!is_null($this->user)) {
            $builder->where("(username = '" .$this->user['username']. "' OR gid IN ('".implode("', '", $this->user['groups'])."')) ");
        }
        $topic = $builder->first();

        if(!$topic) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.topic_not_accessible'));
        }

        $messageModel = new MessageModel();
        $messages = $messageModel->where('tid', $tid)->where('pid' , '')->orderBy('date', 'desc')->paginate(20);

        if(!$messages) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.no_message_found'));
        }
        $this->data['messages'] = $messages;
        $this->data['pager'] = $messageModel->pager;
        $this->data['page_title'] = $topic['title'];
        $this->data['topic'] = $topic;

        return view('\Serasera\Forum\Views\topic_show', $this->data);
    }

    public function edit($tid) {
        $topicModel = new TopicModel();

        if(isset($this->user->level['forum']) && $this->user->level['forum'] >= LEVEL_EDIT) {
            $topic = $topicModel->when(['tid' => $tid])->first();
        } else {
            $topic = $topicModel->when(['tid' => $tid, 'username' => $this->user['username']])->first();
        }

        if(!$topic) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.topic_not_accessible'));
        }

        $this->data['page_title'] = lang('Forum.edit_topic');
        $this->data['topic'] = $topic;

        return view('\Serasera\Forum\Views\topic_edit', $this->data);
    }
}
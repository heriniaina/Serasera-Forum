<?php

namespace Serasera\Forum\Controllers;

use Serasera\Base\Controllers\BaseController;
use Serasera\Forum\Models\MessageModel;
use Serasera\Forum\Models\TopicModel;

class MessageController extends BaseController
{

    public function show($mid, $num = 0)
    {
        
        $messageModel = new MessageModel();

        $message = $messageModel->where('mid', $mid)->first();

        if (!$message) {
            return redirect()->to('forum/topic')->with('error', lang('Forum.message_not_found'));
        }

        if($num > 0) {
            $uri = current_url(true);
            $fragment = $uri->getFragment() ?? '';
            //legacy 
            return redirect()->to('forum/message/' . $mid . '?page=' . (intdiv($num, 20) + 1) );
        }

        $this->data['page_title'] = $message['title'];

        $topicModel = new TopicModel();
        $builder = $topicModel->where('tid', $message['tid']);
        if (!is_null($this->user)) {
            $builder->where("(username = '" . $this->user->username . "' OR gid IN ('" . implode("', '", $this->user->groups) . "')) ");
        }
        $topic = $builder->first();

        if (!$topic) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.topic_not_accessible'));
        }

        $this->data['topic'] = $topic;

        $this->data['messages'] = $messageModel->where('mid', $mid)->orWhere('pid', $mid)->orderBy('id')->paginate(20);
        $this->data['pager'] = $messageModel->pager;

        //counter 
        $session_count_name = 'count_message_' . $mid;
        if ($this->session->{$session_count_name} != $mid) {
            $this->session->set($session_count_name, $mid);
            $messageModel->allowCallbacks(false)->where('mid', $mid)->orWhere('pid', $mid)->set(['hits' => $message['hits'] + 1])->update();
        }

        return view('\Serasera\Forum\Views\message_show', $this->data);
    }
}
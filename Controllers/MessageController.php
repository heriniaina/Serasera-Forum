<?php

namespace Serasera\Forum\Controllers;

use CodeIgniter\Events\Events;
use Serasera\Base\Controllers\BaseController;
use Serasera\Base\Models\NotificationModel;
use Serasera\Forum\Models\ImageModel;
use Serasera\Forum\Models\MessageModel;
use Serasera\Forum\Models\TopicModel;
use Genert\BBCode\BBCode;

class MessageController extends BaseController
{

    public function index()
    {

        $messageModel = new MessageModel();

        $builder = $messageModel->getMessagesWithTopic()->where('m.pid', '');
        $this->data['page_title'] = lang('Forum.message_list');
        $this->data['page_description'] = '';

        if ($this->request->getGet('title')) {
            $lohateny_array = explode(' ', $this->request->getGet('title'));
            foreach ($lohateny_array as $lohateny) {
                if (trim($lohateny) != '')
                    $builder->like('m.title', trim($lohateny));
            }
            $this->data['page_description'] .= ', ' . lang('Forum.title_containing_', [esc($this->request->getGet('title'))]);
        }
        if ($this->request->getGet('message')) {
            foreach (explode(' ', $this->request->getGet('message')) as $msg) {
                if ((trim($msg)) != '') {
                    $builder->like('m.message', trim($msg));
                }
            }
            $this->data['page_description'] .= ', ' . lang('Forum.message_containing_', [esc($this->request->getGet('vetso'))]);
        }
        if ($this->request->getGet('username')) {
            $builder->where('m.username', trim($this->request->getGet('username')));
            $this->data['page_description'] .= ', ' . lang('Forum.posted_by', [esc($this->request->getGet('username'))]);
        }

        if (empty($this->data['page_description'])) {
            $this->data['page_description'] = lang('Forum.all_message_list');
        } else {
            $this->data['page_description'] = lang('Forum.message_list') . $this->data['page_description'];
        }

        if ($this->request->getGet('sort')) {
            if ($this->request->getGet('sort') == 'title') {
                $builder->orderBy('m.title');
                $this->data['page_description'] .= ', ' . lang('Forum.ordered_by', [strtolower(lang('Forum.title_order'))]);
            } elseif ($this->request->getGet('sort') == 'mpanoratra') {
                $builder->orderBy('m.username');
                $this->data['page_description'] .= ', ' . lang('Forum.ordered_by', [strtolower(lang('Forum.username_order'))]);
            }
        } else {
            $builder->orderBy('m.date', 'desc');
            $this->data['page_description'] .= ', ' . lang('Forum.ordered_by', [lang('Forum.date_order')]);
        }



        $this->data['messages'] = $builder->paginate(20);
        $this->data['pager'] = $messageModel->pager;
    

        return view('\Serasera\Forum\Views\message_index', $this->data);
    }

    public function create($tid = false)
    {

        if (
            $this->request->getMethod() === 'post' && $this->validate([
                'message' => 'required',
                'tid' => 'required'
            ], [
                    'message' => ['required' => lang('Forum.message_required')],
                    'tid' => ['required' => lang('Forum.topic_required')]
                ])
        ) {

            $time = time();
            $mid = uniqid('m');
            $data = [
                'mid' => $mid,
                'pid' => '',
                'tid' => $this->request->getPost('tid'),
                'title' => $this->request->getPost('title') ?? substr(trim(strip_tags($this->request->getPost('message'))), 0, 50) . '...',
                'message' => strip_tags($this->request->getPost('message')),
                'email' => $this->user['email'],
                'username' => $this->user['username'],
                'date' => time(),
                'replies' => 0,
                'last_date' => $time,
                'hits' => 0,
                'last_username' => $this->user['username'],
                'last_mid' => $mid,
            ];

            $messageModel = new MessageModel();
            Events::trigger('serasera_forum_message_post', $data);
            $messageModel->insert($data);
            (new TopicModel())->where('tid', $data['tid'])->set(['last_message' => $data['message'], 'last_mid' => $data['last_mid'], 'last_username' => $data['username'], 'last_date' => $data['date'], 'messages' => 'messages + 1'])->update();


            return redirect()->to('forum/topic/' . $data['tid'])->with('message', lang('Forum.message_inserted'));
        }

        $this->data['validation'] = $this->validator;

        $this->data['topics'] = (new TopicModel())->where("(username = '" . $this->user['username'] . "' OR gid IN ('" . implode("', '", $this->user['groups']) . "')) ")->orderBy('title')->findAll();
        $this->data['tid'] = $tid;
        $this->data['page_title'] = lang('Forum.create_new_post');

        return view('\Serasera\Forum\Views\message_create', $this->data);
    }

    public function reply($mid, $quote = 0)
    {

        $messageModel = new MessageModel();

        $message = $messageModel->where('mid', $mid)->first();

        if (!$message) {
            return redirect()->to('forum/topic')->with('error', lang('Forum.message_not_found'));
        }

        $topicModel = new TopicModel();
        $builder = $topicModel->where('tid', $message['tid']);
        if (!is_null($this->user)) {
            $builder->where("(username = '" . $this->user['username'] . "' OR gid IN ('" . implode("', '", $this->user['groups']) . "')) ");
        }
        $topic = $builder->first();

        if (!$topic) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.topic_not_accessible'));
        }

        if (
            $this->request->getMethod() === 'post' && $this->validate(
                ['message' => 'required'],
                ['message' => lang('Forum.message_required')]
            )
        ) {

            $data = [];

            $data['mid'] = uniqid('mid');
            $data['pid'] = $mid;
            $data['tid'] = $message['tid'];
            $data['title'] = 'Re: ' . $message['title'];
            $data['message'] = $this->request->getPost('message');
            $data['username'] = $this->user['username'];
            $data['email'] = $this->user['email'];
            $data['date'] = time();
            $data['last_date'] = $data['date'];
            $data['last_username'] = $data['username'];

            //get page num 
            $count = $messageModel->where('pid', $mid)->orWhere('mid', $mid)->countAllResults();

            $data['last_mid'] = $data['pid'] . '?page=' . (intdiv($count, 20) + 1) . "#" . $data['mid'];

            Events::trigger('serasera_forum_message_reply', $data);
            $messageModel->insert($data);

            (new TopicModel())->where('tid', $data['tid'])->set(['last_message' => $data['message'], 'last_mid' => $data['last_mid'], 'last_username' => $data['username'], 'last_date' => $data['date'], 'messages' => 'messages + 1'])->update();

            //update thread
            $messageModel->where('mid', $mid)->set(['last_message' => $data['message'], 'last_mid' => $data['last_mid'], 'last_username' => $data['username'], 'last_date' => $data['date'], 'replies' => 'replies + 1'])->update();

            //notify 
            $thread = $messageModel->where('mid', $mid)->orWhere('pid', $mid)->orderBy('date')->findAll();
            $notify = [];
            $i = 0;
            foreach ($thread as $t) {
                if ($t['username'] != $this->user['username']) {
                    //notify only if not the sender 
                    if ($i == 0) {
                        //thread owner
                        $notify[$t['username']] = [
                            'username' => $t['username'],
                            'message' => 'Forum.notification_message_replied_to_owner',
                            'args' => json_encode(['titre' => $message['title'], 'username' => $this->user['username']]),
                            'link' => 'forum/message/' . $data['last_mid']
                        ];
                    } else {
                        $notify[$t['username']] = [
                            'username' => $t['username'],
                            'message' => 'Forum.notification_message_replied_to_participants',
                            'args' => json_encode(['titre' => $message['title'], 'username' => $this->user['username']]),
                            'link' => 'forum/message/' . $data['last_mid']
                        ];
                    }
                }
                $i++;
            }

            (new NotificationModel())->insertBatch($notify);

            //redirect 
            return redirect()->to('forum/message/' . $mid)->with('message', lang('Forum.reply_sent_successfully'));

        }

        if (intval($quote) > 0) {
            $quoteMessage = $messageModel->find($quote);
            if ($quoteMessage) {

                $this->data['quote'] = $quoteMessage;
            }
        }

        $this->data['validation'] = $this->validator;
        $this->data['page_title'] = lang('Forum.reply_to', [$message['username']]);
        $this->data['message'] = $message;


        $bbCode = new BBCode();
        $bbCode->addParser(
            'custom-quote',
            '/\[quote\=(.*?)\](.*?)\[\/quote\]/s',
            '<blockquote><em class="author">$1:</em><br />$2</blockquote>',
            '$2'
        )
            ->addParser('ulist', '/\[ul\](.*?)\[\/ul\]/s', '<ul>$1</ul>', '$1')
            ->addParser('olist', '/\[ol\](.*?)\[\/ol\]/s', '<ol>$1</ol>', '$1')
            ->addParser('color', '/\[color\=(.*?)\](.*?)\[\/color\]/s', '<span style="color: $1;">$2</span>', '$2')
            ->addParser('list', '/\[li\](.*?)\[\/li\]/s', '<li>$1</li>', '$1');

        $this->data['bbCode'] = $bbCode;

        return view('\Serasera\Forum\Views\message_reply', $this->data);

    }
    public function show($mid, $num = 0)
    {

        $messageModel = new MessageModel();

        $message = $messageModel->where('mid', $mid)->first();

        if (!$message) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.message_not_found'));
        }

        //has to be a thread 
        if (!empty($message['pid'])) {
            //alway go to root
            return redirect()->to('forum/message/' . $message['pid']);
        }

        if (intval($num) > 0) {
            //$uri = current_url(true);
            //$fragment = '#' . ($uri->getFragment() ?? '');
            //legacy 
            return redirect()->to('forum/message/' . $mid . '?page=' . (intdiv($num, 20) + 1)); //  . $fragment);
        }

        $this->data['page_title'] = $message['title'];

        $topicModel = new TopicModel();
        $builder = $topicModel->where('tid', $message['tid']);
        if (!is_null($this->user)) {
            $builder->where("(username = '" . $this->user['username'] . "' OR gid IN ('" . implode("', '", $this->user['groups']) . "')) ");
        }
        $topic = $builder->first();

        if (!$topic) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.topic_not_accessible'));
        }

        $this->data['topic'] = $topic;
        $this->data['message'] = $message;

        $this->data['messages'] = $messageModel->where('mid', $mid)->orWhere('pid', $mid)->orderBy('id')->paginate(20);
        $this->data['pager'] = $messageModel->pager;

        //counter 
        $session_count_name = 'count_message_' . $mid;
        if ($this->session->{$session_count_name} != $mid) {
            $this->session->set($session_count_name, $mid);
            $messageModel->allowCallbacks(false)->where('mid', $mid)->orWhere('pid', $mid)->set(['hits' => $message['hits'] + 1])->update();
        }
        $bbCode = new BBCode();
        $bbCode->addParser(
            'custom-quote',
            '/\[quote\=(.*?)\](.*?)\[\/quote\]/s',
            '<blockquote><em class="author">$1:</em><br />$2</blockquote>',
            '$2'
        )
            ->addParser('ulist', '/\[ul\](.*?)\[\/ul\]/s', '<ul>$1</ul>', '$1')
            ->addParser('olist', '/\[ol\](.*?)\[\/ol\]/s', '<ol>$1</ol>', '$1')
            ->addParser('color', '/\[color\=(.*?)\](.*?)\[\/color\]/s', '<span style="color: $1;">$2</span>', '$2')
            ->addParser('list', '/\[li\](.*?)\[\/li\]/s', '<li>$1</li>', '$1');
        $this->data['bbCode'] = $bbCode;


        return view('\Serasera\Forum\Views\message_show', $this->data);
    }

    public function delete($mid)
    {
        $messageModel = new MessageModel();

        $message = $messageModel->where('mid', $mid)->first();

        if (!$message) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.message_not_found'));
        }
        $isadmin = isset($this->user['level']) && $this->user['level']['forum'] >= LEVEL_EDIT;
        if ($messageModel->where('pid', $mid)->countAllResults() > 0 && !$isadmin) {
            return redirect()->back()->withInput()->with('error', lang('Forum.cannot_be_deleted_replied'));
        }


        echo $mid;
    }

    public function image()
    {
        $validationRule = [
            'image' => [
                'label' => 'Image File',
                'rules' => 'uploaded[image]'
                . '|is_image[image]'
                . '|mime_in[image,image/jpg,image/jpeg,image/gif,image/png,image/webp]'
                . '|max_size[image,5000]',
            ],
        ];
        if (!$this->validate($validationRule)) {
            $data = ['success' => false, 'error' => true, 'msg' => $this->validator->getErrors()];
            return $this->response->setJSON($data);
        }



        $img = $this->request->getFile('image');
        if (!$img->hasMoved()) {
            $imageModel = new ImageModel();

            $directory = date('Y') . '/' . date('m') . '/' . date('d');
            $img->move(ROOTPATH . 'public/media/images/' . $directory . '/o/');

            //600 m o
            if (!file_exists(ROOTPATH . 'public/media/images/' . $directory . '/m/')) {
                mkdir(ROOTPATH . 'public/media/images/' . $directory . '/m/', 0777, true);
            }
            $image = \Config\Services::image()
                ->withFile(ROOTPATH . 'public/media/images/' . $directory . '/o/' . $img->getName())
                ->resize(600, 600, true, 'width')
                ->save(ROOTPATH . 'public/media/images/' . $directory . '/m/' . $img->getName())
            ;

            $data = [
                'username' => $this->user['username'],
                'file' => site_url('media/images/' . $directory . '/m/' . $img->getName()),
                'date' => time(),
            ];
            $data['id'] = $imageModel->insert($data, true);

            return $this->response->setJSON($data);
        }

        $data = ['success' => false, 'error' => true, 'msg' => 'The file has already been moved.'];
        return $this->response->setJSON($data);
    }

    public function edit($mid)
    {
        $messageModel = new MessageModel();

        $message = $messageModel->where('mid', $mid)->first();

        if (!$message) {
            return redirect()->to('forum/topics')->with('error', lang('Forum.message_not_found'));
        }
        $isadmin = isset($this->user['level']) && $this->user['level']['forum'] >= LEVEL_EDIT;
        if($message['username'] != $this->user['username'] &&  !$isadmin) {
            return redirect()->to('forum/message/' . $mid)->with('error', lang('Forum.only_owner_can_modify'));
        }
        if ($messageModel->where('pid', $mid)->countAllResults() > 0 && !$isadmin) {
            return redirect()->to('forum/message/' . $mid)->with('error', lang('Forum.cannot_be_modified_replied'));
        }

        if (
            $this->request->getMethod() === 'post' && $this->validate([
                'message' => 'required',
                'tid' => 'required'
            ], [
                    'message' => ['required' => lang('Forum.message_required')],
                    'tid' => ['required' => lang('Forum.topic_required')]
                ])
        ) {



            $data = [
                'tid' => $this->request->getPost('tid'),
                'title' => $this->request->getPost('title') ?? substr(trim(strip_tags($this->request->getPost('message'))), 0, 50) . '...',
                'message' => strip_tags($this->request->getPost('message')),
            ];

            $messageModel = new MessageModel();
            Events::trigger('serasera_forum_message_edit', $data);
            $messageModel->update($message['id'], $data);
            
            return redirect()->to('forum/topic/' . $data['tid'])->with('message', lang('Forum.message_inserted'));
        }

        $this->data['validation'] = $this->validator;

        $this->data['message'] = $message;
        $this->data['topics'] = (new TopicModel())->where("(username = '" . $this->user['username'] . "' OR gid IN ('" . implode("', '", $this->user['groups']) . "')) ")->orderBy('title')->findAll();
        $this->data['tid'] = $message['tid'];
        $this->data['page_title'] = lang('Forum.modify_post');

        return view('\Serasera\Forum\Views\message_edit', $this->data);
    }
}
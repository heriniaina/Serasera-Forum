<?php

namespace Serasera\Forum\Controllers;

use Serasera\Base\Controllers\BaseController;
use Serasera\Forum\Models\MessageModel;

class RssController extends BaseController
{

    public function index()
    {
        helper('xml');
        $messageModel = new MessageModel();
        $rows = $messageModel->getMessagesWithTopic()->where('t.gid', 0)->where('m.pid', '')->orderBy('m.id', 'desc')->findAll(20);
        if ($rows) {
            $data['encoding'] = 'utf-8';
            $data['feed_name'] = "forum.serasera.org";
            $data['feed_url'] = base_url();
            $data['page_description'] = "description";
            $data['page_language'] = "mg";
            $data['creator_email'] = "hery@serasera.org";


            foreach ($rows as $key => $row) {
                $contents[$key]['title'] = xml_convert($row['title']);
                $contents[$key]['url'] = site_url('forum/message/' . $row['mid']);
                //$contents[$key]['file'] = site_url('tononkalo/' . $row['uri'] . '/sary');
                if (strlen($row['message']) > 300) {
                    $body = nl2br(strip_tags(substr($row['message'], 0, strpos($row['message'], ' ', 300)))) . "... ";
                } else {
                    $body = nl2br(strip_tags($row['message']));
                }

                $contents[$key]['body'] = $body;
                $contents[$key]['date'] = (isset($row['date'])) ? $row['date'] : '';
            }
            $data['contents'] = $contents;


            $body = view('\Serasera\Forum\Views\rss_index', $data);
            return $this->response->setHeader('Content-Type', 'application/rss+xml')->setBody($body);
        }
    }


    public function updates()
    {
        helper('xml');
        $messageModel = new MessageModel();
        $rows =  (new MessageModel())->orderBy('date', 'desc')->findAll(10);
        if ($rows) {
            $data['encoding'] = 'utf-8';
            $data['feed_name'] = "forum.serasera.org";
            $data['feed_url'] = base_url();
            $data['page_description'] = "description";
            $data['page_language'] = "mg";
            $data['creator_email'] = "hery@serasera.org";


            foreach ($rows as $key => $row) {
                $contents[$key]['title'] = xml_convert($row['title']);
                $contents[$key]['url'] = site_url('forum/message/' . $row['mid']);
                //$contents[$key]['file'] = site_url('tononkalo/' . $row['uri'] . '/sary');
                if (strlen($row['message']) > 300) {
                    $body = nl2br(strip_tags(substr($row['message'], 0, strpos($row['message'], ' ', 300)))) . "... ";
                } else {
                    $body = nl2br(strip_tags($row['message']));
                }

                $contents[$key]['body'] = $body;
                $contents[$key]['date'] = (isset($row['date'])) ? $row['date'] : '';
            }
            $data['contents'] = $contents;


            $body = view('\Serasera\Forum\Views\rss_index', $data);
            return $this->response->setHeader('Content-Type', 'application/rss+xml')->setBody($body);
        }
    }

    public function daily()
    {
        helper('xml');
        if (!$row = cache('rss_daily_rand')) {

            $vetsoModel = new VetsoModel();
            $row = $vetsoModel->getVetsoList()->where('v.draft', 0)->orderBy('v.id', 'RANDOM')->first();

            // Save into the cache for 5 minutes
            cache()->save('rss_daily_rand', $row, 60*60*24);
        }


        if ($row) {
            $data['encoding'] = 'utf-8';
            $data['feed_name'] = "vetso.serasera.org";
            $data['feed_url'] = base_url();
            $data['page_description'] = "description";
            $data['page_language'] = "mg";
            $data['creator_email'] = "hery@serasera.org";


            $key = 0;
            $contents[$key]['title'] = xml_convert($row['titre']);
            $contents[$key]['url'] = site_url('tononkalo/' . $row['uri']);
            // if (strlen($row['vetso']) > 300) {
            //     $body = nl2br(strip_tags(substr($row['vetso'], 0, strpos($row['vetso'], ' ', 300)))) . "... ";
            // } else {
                $body = nl2br(strip_tags($row['vetso']));
            // }

            $body = "<img src='" . site_url('tononkalo/' . $row['uri'] . '/sary') . "' /> <br />" . $body;
            $contents[$key]['file'] = site_url('tononkalo/' . $row['uri'] . '/sary');

            $contents[$key]['body'] = $body;
            $contents[$key]['date'] = (isset($row['daty'])) ? $row['daty'] : '';

            $data['contents'] = $contents;


            $body = view('\Serasera\Vetso\Views\rss_index', $data);
            return $this->response->setHeader('Content-Type', 'application/rss+xml')->setBody($body);
        }
    }
}
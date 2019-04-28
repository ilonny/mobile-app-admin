<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class EsController extends Controller
{

    // public $enableCsrfValidation = false;

    public function actionIndex()
    {
        require('phpQuery-onefile.php');
        $html_src = file_get_contents('https://sadhusangamexico.wordpress.com/category/scsmath/');
        $document = \phpQuery::newDocument($html_src);
        // $root_span = $document->find('span.m1_grey');
        // $parent_td = $root_span->parent();
        // // $all_img = $parent_td->find('img');
        // // foreach ($parent_td->find('img') as $key => $img) {
        // //     $img = pq($img);
        // //     // var_dump($img->attr('src'));die();
        // //     $attr = 'http://scsmath.com/' . $img->attr('src');
        // //     $img->attr('src', $attr);
        // // }
        // $blocks = $parent_td->find('blockquote.list');
        // echo $parent_td->html();
        $content  = $document->find(".site-content");
        $this->layout = 'api';
        return $this->render('mainpage', [
            'html' => $content->html(),
        ]);
        // var_dump($blocks[0]->html());
        // die();
        // $response = [];
        // foreach ($blocks as $key => $block) {
        //     // var_dump($block);die();
        //     var_dump($block->html);die();
        //     if ($block->previousSibling->tagName == 'p') {
        //         var_dump(123);
        //     } else {
        //         var_dump(45);
        //     }
        //     die();
        // }
        // var_dump($parent_td->html());

        // var_dump($root_span->html());
        // return $html_src;
    }

    public function actionSite2(){
        \Yii::$app->view->registerMetaTag([
            'http-equiv' => 'Content-Type',
            'content' => 'text/html; charset=iso-8859-1"',
        ]);
        require('phpQuery-onefile.php');
        $html_src = file_get_contents('http://www.paramakaruna.org.ve/pag-mision.htm');
        $document = \phpQuery::newDocument($html_src);
        $content  = $document->find("table")->find('table')->find('table');
        // echo $content;die();
        foreach ($content->find('img') as $key => $img) {
            $img = pq($img);
            $attr = 'http://www.paramakaruna.org.ve/' . $img->attr('src');
            $img->attr('src', $attr);
        }
        foreach ($content->find('a') as $key => $a) {
            $a = pq($a);
            $attr = 'http://www.paramakaruna.org.ve/' . $a->attr('src');
            $a->attr('href', $attr);
        }
        $this->layout = 'api';
        return $this->render('mainpage-2', [
            'html' => mb_convert_encoding($content->html(), 'UTF-8',
                        mb_detect_encoding($content->html(), 'UTF-8, ISO-8859-1', true)),
        ]);
    }
}

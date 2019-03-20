<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class ScsController extends Controller
{

    // public $enableCsrfValidation = false;

    public function actionIndex()
    {
        require('phpQuery-onefile.php');
        $html_src = file_get_contents('http://scsmath.com');
        $document = \phpQuery::newDocument($html_src);
        $root_span = $document->find('span.m1_grey');
        $parent_td = $root_span->parent();
        $blocks = $parent_td->find('blockquote.list');
        // echo $parent_td->html();
        $this->layout = 'api';
        return $this->render('mainpage', [
            'html' => $parent_td->html(),
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

}

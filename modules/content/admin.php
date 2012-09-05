<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author user
 */
class ContentAdminController extends Controller {
    /**
     * @todo Home page
     */
    public function indexAction() {
        $this->template = dirname(__FILE__) . '/views/index.tpl.php';
    }
    /**
     * @todo Category page
     */
    public function cateAction($cid) {
        $this->template = dirname(__FILE__) . '/views/cate.tpl.php';
    }
    /**
     * @todo Node page
     */
    public function nodeAction($nid) {
        $this->template = dirname(__FILE__) . '/views/node.tpl.php';
    }
}

?>

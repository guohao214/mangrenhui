<?php

class Article extends FrontendController
{
  public function look()
  {
     $this->view('article/index');
  }
}
<?php

/**
 * ca actions.
 *
 * @package    OpenPNE
 * @subpackage ca
 * @author     Your name here
 */
class caActions extends sfActions
{
  public function executeUpdateActivity($r)
  {
    if ($r->isMethod(sfWebRequest::POST))
    {
      $result = false;
      try{
        $obj = new ActivityData();
        switch($r->getParameter("foreign_table")){
          case null:
          case "":
          case "community":
            $obj->foreign_table = $r->getParameter("foreign_table");
            break;
          default:
            return $this->renderText(json_encode(array("result" => "error","message" => "INVALID FOREIGN_TABLE")));
        }
        $obj->member_id = $this->getUser()->getMemberId();
        $obj->foreign_id = $r->getParameter("foreign_id");
        $obj->body = $r->getParameter("body");
        $obj->save();
        $result = true;
      }catch(Exception $e){
        $result = false;
        $msg = $e->getMessage();
      }
      if($result){
        $m = Doctrine::getTable("Member")->find($this->getUser()->getMemberId());
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Tag','sfImage','Asset','opUtil'));
        $image = $m->getImage();
        if($image->getFileId()){
         $tag = op_image_tag_sf_image($image->getFile(), array('size' => '48x48'));
        }else{
         $tag = op_image_tag($image->getUri(), array('width' => 48, 'height' => 48));
        }
        //sf_image_path
        $tag = "http://www.tejimaya.com/wp-content/themes/tejimaya/img/index/tejima.jpg";
        $opt = array("image" => $tag);
        $arr = array_merge($opt,$m->toArray(),$obj->toArray());
        return $this->renderText(json_encode($arr));
      }else{
        //render error
        return $this->renderText(json_encode(array("result" => "error","msg" => $msg)));
      }
    }
  }
}

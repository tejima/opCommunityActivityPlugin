<?php

class caComponents extends sfComponents
{
  public function executeActivityBox(sfWebRequest $request)
  {//FIXME 多分このメソッドは要らない
    $id = $request->getParameter('id', $this->getUser()->getMemberId());
    $this->activities = Doctrine::getTable('ActivityData')->getActivityList($id, null, $this->gadget->getConfig('row'));
    $this->member = Doctrine::getTable('Member')->find($id);
    $this->isMine = ($id == $this->getUser()->getMemberId());
  }
  //public function executeAllMemberActivityBox(sfWebRequest $request)
  public function executeTestAct(sfWebRequest $request)
  {
    $community_id = $request->getParameter("id");
    $this->community_id = $community_id;
    $this->activities = Doctrine_Query::create()->from("ActivityData ad")->where("ad.foreign_table = ?","community")->andWhere("ad.foreign_id = ?",$community_id)->limit(10)->orderBy("ad.created_at desc")->execute();
    //echo "DUMP";
    //var_dump($this->activities);
    //exit();
    //$this->activities = Doctrine::getTable('ActivityData')->getAllMemberActivityList(10);
  }
}

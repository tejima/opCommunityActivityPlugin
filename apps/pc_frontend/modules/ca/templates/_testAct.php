<?php use_javascript('http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.6.2.min.js') ?>
<form method="POST" action="">
  <textarea cols="50" rows="4" id="activity_body" ></textarea>
  <input value="community" type="hidden" name="foreign_table" id="foreign_table" />
  <input value="<?php echo $community_id;?>" type="hidden" name="foreign_id" id="foreign_id" />
  <div id="share" style="font-size: 150%;">共有する</div>
</form>
<script>

 $().ready(function(){
   $("div#share").click(function(){
     var postdata = { body: $("#activity_body").val(), foreign_table: $("#foreign_table").val(), foreign_id: $("#foreign_id").val()};
     $("#activity_body").val("");
     $.post(
       "<?php echo $baseUrl; ?>/ca/updateActivity", //FIXME symfonyスタイルのアクション指定に変更
        postdata,
        function(data_json){
         data = $.parseJSON(data_json);
         //FIXME 返答データの検証
         al = createActivityLine(data);
         $("#activityBox_timeline").html(al.html() + $("#activityBox_timeline").html());
     });
   });
 });
function createActivityLine(data){
  template = $(".activity.sample").clone().removeClass("sample");
  template.find(".bodyText").text(data.body);
  template.find(".box_memberImage img").attr("alt",data.name);
  template.find(".name a").text(data.name);
  template.find("a[href*='MEMBER_ID']").each(function(){
    $(this).attr("href",$(this).attr("href").replace("MEMBER_ID",data.member_id));
  });
  template.find("a img").attr("src",data.image);
  member_id = $("#member_id_you").text();
  
  if(data.member_id == member_id){
    //show delete block
    template.find(".delete").css("display","block");
  }else{
    //do nothing
  }
  
  return template;
}
</script>
<div style="display: none;" id="member_id_you"><?php echo $sf_user->getMemberId(); ?></div>
<div style="display: none;">
<li class="activity sample"> 
<div class="box_memberImage"> 
<p><a href="/member/MEMBER_ID"><img alt="MEMBER_NAME" src="/images/no_image.gif" height="48" width="48" /></a></p> 
</div> 
<div class="box_body"> 
<p> 
<span class="content"> 
<strong class="name"><a href="/member/MEMBER_ID">MEMBER_NAME</a></strong> 
<span class="bodyText">DUMMY</span> 
</span> 
<span class="info"> 
<span class="time">5秒前</span> 
</span> 
</p> 
<ul class="operation"> 
<li class="delete" style="display: none;"><a title="Delete this activity" href="/member/deleteActivity/id/MEMBER_ID">削除する</a></li> 
</ul> 
</div> 
</li> 
</div>

<?php $id = 'activityBox' ?>
<?php $id .= isset($gadget) ? '_'.$gadget->getId() : '' ?>
<?php if (count($activities) || isset($form)): ?>
<?php $params = array(
  'activities' => $activities,
  //'gadget' => $gadget,
  'gadget' => null, //FIXME CHECK THIS
  'title' => __("SNS Member's %activity%", array(
    '%activity%' => $op_term['activity']->titleize()->pluralize()
  )),
  'moreUrl' => 'member/showAllMemberActivity'
) ?>
<?php if (isset($form)): ?>
<?php $params['form'] = $form ?>
<?php endif; ?>

<div class="box_list">
<ol id="<?php echo $id ?>_timeline" class="activities">
<?php use_helper('opActivity') ?>
<?php foreach ($activities as $activity): ?>

<li class="activity">
<div class="box_memberImage">
<p><?php echo link_to(op_image_tag_sf_image($activity->getMember()->getImageFileName(), array('alt' => sprintf('[%s]', $activity->getMember()), 'size' => '48x48')), '@obj_member_profile?id='.$activity->getMemberId()) ?></p>
</div>
<div class="box_body">
<p>
<span class="content">
<strong class="name"><?php echo op_link_to_member($activity->getMember()) ?></strong>
<span class="bodyText"><?php echo op_activity_body_filter($activity) ?></span>
</span>
<span class="info">
<span class="time"><?php echo $time = op_format_activity_time(strtotime($activity->getCreatedAt())) ?>
<?php if ($activity->getSource()): ?>
 from <?php echo link_to_if($activity->getSourceUri(), $activity->getSource(), $activity->getSourceUri()) ?>
<?php endif; ?>
</span>
<?php if ($activity->getPublicFlag() != ActivityDataTable::PUBLIC_FLAG_SNS): ?>
<span class="public_flag"><?php echo __('Public flag') ?> : <?php echo $activity->getPublicFlagCaption() ?></span>
<?php endif; ?>
</span>
</p>
<?php
$operationItems = array();
if (!isset($isOperation) || $isOperation)
{
  if ($activity->getMemberId() == $sf_user->getMemberId())
  {
    $operationItems[] = array(
      'class' => 'delete',
      'body'  => link_to(__('Delete'), 'member/deleteActivity?id='.$activity->getId(), array('title' => __('Delete this activity of %time%', array('%time%' => $time)))),
    );
  }
}
?>
<?php if (0 < count($operationItems)): ?>
<ul class="operation">
<?php
foreach ($operationItems as $item)
{
  if (is_array($item) && isset($item['body']))
  {
    printf("<li%s>%s</li>\n", isset($item['class']) ? sprintf(' class="%s"', $item['class']) : '', $item['body']);
  }
}
?>
</ul>
<?php endif; ?>
</div>
</li>

<?php endforeach; ?>
</ol>
</div>
<?php endif; ?>


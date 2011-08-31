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
       "/ca/updateActivity", //FIXME symfonyスタイルのアクション指定に変更
        postdata,
        function(data_json){
         al = createActivityLine($.parseJSON(data_json));
         $("#activityBox_timeline").html(al.html() + $("#activityBox_timeline").html());
     });
   });
 });
function createActivityLine(data){
  template = $(".activity.sample").clone().removeClass("sample");
  template.find(".bodyText").text(data.body);
  template.find(".name a").text(data.name);
  template.find(".name a").attr("href","/member/" + data.member_id);
  template.find("a img").attr("src",data.image);
  return template;
}
</script>

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
<?php foreach ($activities as $activity): ?>
<?php include_partial('default/activityRecord', array('activity' => $activity)); ?>
<?php endforeach; ?>
</ol>
</div>
<?php endif; ?>

<div style="display: none;">
<li class="activity sample"> 
<div class="box_memberImage"> 
<p><a href="/member/1"><img alt="[1-手嶋]" src="/images/no_image.gif" height="48" width="48" /></a></p> 
</div> 
<div class="box_body"> 
<p> 
<span class="content"> 
<strong class="name"><a href="/member/1">DUMMY</a></strong> 
<span class="bodyText">DUMMY</span> 
</span> 
<span class="info"> 
<span class="time">たった今</span> 
</span> 
</p> 
<ul class="operation"> 
<li class="delete"><a title="Delete this activity of 3分前" href="/member/deleteActivity/id/27">削除する</a></li> 
</ul> 
</div> 
</li> 
</div>

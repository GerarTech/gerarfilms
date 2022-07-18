<?php 
$list=array();
foreach ($reviews as $key => $review) {
	$a["id"]=$review->getId();
	$a["user"]=$review->getUser()->getName();
  $a["content"]=$review->getReview();
	$a["rating"]=$review->getValue();
          if($review->getUser()->getMedia() ==  null ){
              $a["image"] = "https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg" ;
          }else{
              if ($review->getUser()->getMedia()->getType()=="link") {
                  $a["image"] = $review->getUser()->getMedia()->getUrl() ;
              }else{
                  $a["image"] = $this['imagine']->filter($review->getUser()->getMedia()->getLink(), 'actor_thumb') ;
              }
          }
	$a["created"]=$view['time']->diff($review->getCreated());
	$list[]=$a;
}
echo json_encode($list, JSON_UNESCAPED_UNICODE);
?>
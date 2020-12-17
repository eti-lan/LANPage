<div class="container-fluid">
                <div class="row">
                    <div class="col-lg-10 col-md-10">
                        <div class="page-header">
                            <h1 id="<?php echo $nav['faq']; ?>"><?php echo $faq['title']; ?></h1>
                        </div>
                    </div>
<?php
	
	$faqlist_query = "SELECT * FROM faq WHERE show = '1' ORDER BY question DESC";
	$faqlist_res = $faq_db->query($faqlist_query);
?>
					<div class="col-lg-10 col-md-10 faq">
					<ul>
<?php 				while ($row = $faqlist_res->fetchArray()) {
						echo "<li>".$row['question']."</li>";
						echo "<ul><li>".$row['answer']."</li></ul>";
					}
?>
					</ul>
					</div>
				</div>
</div>
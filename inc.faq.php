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
					<div class="col-lg-10 col-md-10 faq" id="faqaccordion">
<?php 				while ($row = $faqlist_res->fetchArray()) {
						echo "<h3>".$row['question']."</h3>";
						echo "<div><p>".$row['answer']."</p></div>";
					}
?>
					</div>
				</div>
			</div>
			<script>
				jQuery.noConflict();
				var $j = jQuery;
				
				$j( function() {
					$j( "#faqaccordion" ).accordion();
				} );
			</script>
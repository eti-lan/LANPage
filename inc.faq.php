			<div class="container-fluid">
                <div class="row">
                    <div class="col-lg-10 col-md-10">
                        <div class="page-header">
                            <h1 id="<?php echo $nav['faq']; ?>"><?php echo $faq['title']; ?></h1>
                        </div>
                    </div>

					<div class="col-lg-10 col-md-10">
					<div class="panel-group" id="accordion">
					<?php

                    if (file_exists('faq.xml')) {
                        $sXml = file_get_contents('faq.xml');
                    } else if (file_exists('faq.sample.xml')) {
                        $sXml = file_get_contents('faq.sample.xml');
                    } else {
                        die;
                    }

                    $oXML = simplexml_load_string($sXml);
                    if (!$oXML) {
                        die($downloads['error']);
                    }

                    foreach ($oXML->question as $question) {

                        foreach ($question->lang as $qlang) {
                            if ($qlang == "de" and $lang == "german" or $qlang == "en" and $lang == "english") {
								foreach ($question->title as $title) {
									$qid = $qid +1;
									print '<div class="panel panel-primary"><div class="panel-heading"><h4 data-toggle="collapse" data-parent="#accordionq'.$qid.'" href="#collapseq'.$qid.'" class="panel-title expand">
										   <div class="right-arrow pull-right"><span class="glyphicon glyphicon-plus"></span></div><a href="#">'.$title.'</a></h4></div><div id="collapseq'.$qid.'" class="panel-collapse collapse">';
								}
								foreach ($question->answer  as $answer) {
									$awid = $awid +1;
									print '<div class="panel-body">'.$answer.'</div></div></div>';
								}
							}
                        }
                    }
                    ?>
					</div>
					</div>
				</div>
			</div>
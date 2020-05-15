<div class="col col-md-10">
	<div class="panel panel-default">
		<div class="panel-body">
			<div id="chat_text" style="position:relative;height:400px;overflow:auto;"></div>
		</div>
	</div>


</div>
<div class="col col-md-2">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div id="chat_online"><span class="title"><?php echo $l_user; ?> online</span></div>
		</div>
		<div class="panel-body">
			<div id="online_list"></div>
		</div>
	</div>
</div>

<div class="col col-md-5">
	<!--  -->
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $chat['send_message']; ?><div style="float:right;" id="pm_panel"><span class="glyphicon glyphicon-globe" aria-hidden="true" style="cursor:pointer;" onclick="set_pm('0')" title="<?php echo $chat['send_all']; ?>" alt="<?php echo $chat['send_all']; ?>"></span><span id="prv_text"> <?php echo $chat['send_all']; ?></span></div>
		</div>
		<div class="panel-body">
			<div id="chat_writer">
				<form onsubmit="return false" action="chat/system/send.php" id="writer">
					<div class="input-group">
						<input type="text" name="say" id="say" onkeypress="return evaluateSubmit(event);" class="form-control" /> <span class="input-group-btn">
							<button class="btn btn-warning" type="submit" value="<?php echo $send; ?>" onclick="send();" class="btn btn-warning"><?php echo $chat['send_message']; ?></button>
						</span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="col col-md-2">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $chat['emoticons']; ?></div>
		<div class="panel-body">
			<div id="emoticon">
				<div><?php echo gen_smiley_list(); ?></div>
			</div>
		</div>
	</div>
</div>

<div class="col col-md-3">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $chat['color']; ?></div>
		<div class="panel-body">
			<div id="color">
				<?php echo gen_color_chooser(); ?></div>
		</div>
	</div>
</div>